# prediksi_service.py - Python FastAPI Service untuk Prediksi Siklus Menstruasi

from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel, Field
from typing import Optional
import numpy as np
import joblib
import os
from datetime import datetime, timedelta
import uvicorn

# 1. INISIALISASI FASTAPI

app = FastAPI(
    title="Menstrual Cycle Prediction API",
    description="API untuk memprediksi panjang siklus menstruasi menggunakan Machine Learning",
    version="2.0.0",
    docs_url="/docs",
    redoc_url="/redoc"
)

# CORS untuk bisa diakses Laravel
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# 2. GLOBAL VARIABLES

model = None
scaler = None
feature_names = None

# MAE REAL dari hasil training (dari evaluation.json)
ERROR_MARGIN = 1.72

# Batasan siklus normal (hari)
MIN_CYCLE_DAYS = 20
MAX_CYCLE_DAYS = 45

# 3. DEFINISI REQUEST/RESPONSE MODEL

class UserData(BaseModel):
    """
    Data yang dikirim dari Laravel ke AI.
    CATATAN: Tidak ada 'cycle_length_days' di sini karena itu adalah TARGET yang diprediksi!
    """
    prev_cycle_length: float = Field(..., description="Panjang siklus sebelumnya (dihitung dari selisih tanggal)")
    pain_level: float = Field(..., ge=0, le=10, description="Tingkat nyeri haid (0-10)")
    stress_score_cycle: float = Field(..., ge=0, le=10, description="Tingkat stres selama siklus (0-10)")
    sleep_hours_cycle: float = Field(..., ge=0, le=24, description="Rata-rata jam tidur per hari")
    mood_score: Optional[float] = Field(7, ge=1, le=10, description="Rata-rata mood (1-10)")
    age: Optional[float] = Field(25, ge=12, le=60, description="Usia dalam tahun")
    bmi: Optional[float] = Field(22, ge=15, le=40, description="Indeks Massa Tubuh")
    pcos_diagnosed: Optional[int] = Field(0, ge=0, le=1, description="PCOS (0=Tidak, 1=Ya)")
    birth_control_use: Optional[int] = Field(0, ge=0, le=1, description="KB hormonal (0=Tidak, 1=Ya)")


class PredictionRequest(BaseModel):
    """Request dari Laravel ke AI"""
    start_date: str = Field(..., description="Tanggal mulai haid terakhir (YYYY-MM-DD)")
    user_data: UserData = Field(..., description="Data user untuk prediksi")


class PredictionResponse(BaseModel):
    """Response dari AI ke Laravel"""
    success: bool
    start_date: str
    predicted_cycle_length: float = Field(..., description="PANJANG SIKLUS YANG DIPREDIKSI AI (dalam hari)")
    next_period_date: str = Field(..., description="Perkiraan tanggal haid berikutnya")
    error_margin: float = Field(..., description="Margin error (MAE) dalam hari")
    confidence_level: str = Field(..., description="Tingkat kepercayaan prediksi")
    message: str

# 4. FUNGSI LOAD MODEL (DENGAN PATH YANG BENAR)

def load_models():
    """Memuat model, scaler, dan feature names dari folder data_ready/"""
    global model, scaler, feature_names
    
    # Dapatkan direktori tempat file ini berada
    base_dir = os.path.dirname(os.path.abspath(__file__))
    data_dir = os.path.join(base_dir, 'data_ready')
    
    model_path = os.path.join(data_dir, 'best_model.pkl')
    scaler_path = os.path.join(data_dir, 'scaler.pkl')
    features_path = os.path.join(data_dir, 'feature_names.pkl')
    
    print(f"📂 Base directory: {base_dir}")
    print(f"📂 Data directory: {data_dir}")
    print(f"📂 Model path: {model_path}")
    
    # Cek apakah folder data_ready ada
    if not os.path.exists(data_dir):
        raise FileNotFoundError(f"Folder 'data_ready' tidak ditemukan di {base_dir}")
    
    # Load model
    if os.path.exists(model_path):
        print("📂 Model ditemukan, loading...")
        model = joblib.load(model_path)
        print(f"✅ Model loaded! Type: {type(model).__name__}")
    else:
        raise FileNotFoundError(f"Model tidak ditemukan di {model_path}")
    
    # Load scaler
    if os.path.exists(scaler_path):
        scaler = joblib.load(scaler_path)
        print(f"✅ Scaler loaded!")
    else:
        print(f"⚠️ Scaler tidak ditemukan di {scaler_path}, prediksi akan tanpa scaling")
    
    # Load feature names
    if os.path.exists(features_path):
        feature_names = joblib.load(features_path)
        print(f"✅ Features: {len(feature_names)} fitur")
        print(f"   Feature order: {feature_names}")
    else:
        print(f"⚠️ Feature names tidak ditemukan di {features_path}")
    
    print("\n✅ Service siap menerima request!")

# 5. FUNGSI PREPROCESSING

def preprocess_input(user_data: UserData) -> np.ndarray:
    """
    Mengubah input user menjadi format yang bisa diprediksi model.
    URUTAN FITUR HARUS SAMA PERSIS DENGAN SAAT TRAINING!
    """
    
    # Urutan fitur HARUS persis seperti saat training (dari feature_names.pkl)
    # [0] prev_cycle_length
    # [1] pain_level
    # [2] stress_score_cycle
    # [3] sleep_hours_cycle
    # [4] mood_score
    # [5] age
    # [6] bmi
    # [7] pcos_diagnosed
    # [8] birth_control_use
    
    feature_order = [
        'prev_cycle_length',
        'pain_level',
        'stress_score_cycle',
        'sleep_hours_cycle',
        'mood_score',
        'age',
        'bmi',
        'pcos_diagnosed',
        'birth_control_use'
    ]
    
    # Buat dictionary input
    input_dict = {
        'prev_cycle_length': user_data.prev_cycle_length,
        'pain_level': user_data.pain_level,
        'stress_score_cycle': user_data.stress_score_cycle,
        'sleep_hours_cycle': user_data.sleep_hours_cycle,
        'mood_score': user_data.mood_score if user_data.mood_score is not None else 7,
        'age': user_data.age if user_data.age is not None else 25,
        'bmi': user_data.bmi if user_data.bmi is not None else 22,
        'pcos_diagnosed': user_data.pcos_diagnosed if user_data.pcos_diagnosed is not None else 0,
        'birth_control_use': user_data.birth_control_use if user_data.birth_control_use is not None else 0,
    }
    
    # Buat vector sesuai urutan
    input_vector = [[input_dict[f] for f in feature_order]]
    
    # Apply scaling jika ada
    if scaler is not None:
        input_vector = scaler.transform(input_vector)
        print(f"📊 Data after scaling: {input_vector}")
    
    return np.array(input_vector)

# 6. ENDPOINT PREDIKSI (INTI DARI SEMUA)

@app.on_event("startup")
async def startup_event():
    """Event saat server mulai"""
    print("\n" + "="*60)
    print("🩸 MENSTRUAL CYCLE PREDICTION SERVICE")
    print("="*60)
    print("📌 LOGIKA: AI memprediksi cycle_length_days (panjang siklus)")
    print("📌 Input: prev_cycle_length (dihitung dari selisih tanggal)")
    print("📌 Output: predicted_cycle_length (HASIL PREDIKSI AI)")
    print("="*60)
    
    load_models()
    
    print(f"\n📊 Error Margin (MAE): {ERROR_MARGIN} hari")
    print(f"📊 Batasan siklus: {MIN_CYCLE_DAYS}-{MAX_CYCLE_DAYS} hari")
    print("\n✅ Service siap menerima request!")


@app.post("/api/predict", response_model=PredictionResponse)
async def predict(request: PredictionRequest):
    """
    Endpoint untuk prediksi siklus menstruasi.
    
    INPUT: 
        - start_date: tanggal mulai haid terakhir
        - user_data: 9 fitur (prev_cycle_length, pain_level, stress, sleep_hours, dll)
    
    OUTPUT:
        - predicted_cycle_length: PANJANG SIKLUS YANG DIPREDIKSI (dalam hari)
        - next_period_date: tanggal haid berikutnya
    
    CATATAN PENTING:
        - prev_cycle_length dihitung dari selisih tanggal oleh Laravel, BUKAN input user!
        - cycle_length_days TIDAK PERNAH menjadi input, karena itu adalah TARGET prediksi!
    """
    try:
        print("\n" + "="*50)
        print("📥 MENERIMA REQUEST PREDIKSI")
        print(f"   Start date: {request.start_date}")
        print(f"   Prev cycle length: {request.user_data.prev_cycle_length} hari (dihitung dari selisih tanggal)")
        print(f"   Pain level: {request.user_data.pain_level}")
        print(f"   Stress score: {request.user_data.stress_score_cycle}")
        print(f"   Sleep hours: {request.user_data.sleep_hours_cycle}")
        print("="*50)
        
        # Validasi input dasar
        if request.user_data.prev_cycle_length < MIN_CYCLE_DAYS or request.user_data.prev_cycle_length > MAX_CYCLE_DAYS:
            raise HTTPException(
                status_code=400, 
                detail=f"Panjang siklus sebelumnya harus antara {MIN_CYCLE_DAYS}-{MAX_CYCLE_DAYS} hari. Saat ini: {request.user_data.prev_cycle_length}"
            )
        
        if request.user_data.pain_level < 0 or request.user_data.pain_level > 10:
            raise HTTPException(status_code=400, detail="Tingkat nyeri harus antara 0-10")
        
        if request.user_data.stress_score_cycle < 0 or request.user_data.stress_score_cycle > 10:
            raise HTTPException(status_code=400, detail="Skor stres harus antara 0-10")
        
        if request.user_data.sleep_hours_cycle < 0 or request.user_data.sleep_hours_cycle > 24:
            raise HTTPException(status_code=400, detail="Jam tidur harus antara 0-24")
        
        # Validasi format tanggal
        try:
            start_date = datetime.strptime(request.start_date, '%Y-%m-%d')
        except ValueError:
            raise HTTPException(status_code=400, detail="Format tanggal harus YYYY-MM-DD")
        
        # STEP 1: Preprocessing input
        input_vector = preprocess_input(request.user_data)
        
        # STEP 2: Prediksi menggunakan model AI
        predicted_days = model.predict(input_vector)[0]
        print(f"🤖 Raw prediction: {predicted_days} days")
        
        # STEP 3: Batasi hasil prediksi dalam rentang normal
        predicted_days = max(MIN_CYCLE_DAYS, min(MAX_CYCLE_DAYS, predicted_days))
        print(f"📊 Clipped prediction: {predicted_days} days")
        
        # STEP 4: Hitung tanggal haid berikutnya
        next_date = start_date + timedelta(days=int(round(predicted_days)))
        
        # STEP 5: Hitung rentang error
        min_date = start_date + timedelta(days=int(round(predicted_days - ERROR_MARGIN)))
        max_date = start_date + timedelta(days=int(round(predicted_days + ERROR_MARGIN)))
        
        # STEP 6: Tentukan tingkat kepercayaan
        if ERROR_MARGIN <= 2.0:
            confidence = "Tinggi"
        elif ERROR_MARGIN <= 3.0:
            confidence = "Sedang"
        else:
            confidence = "Rendah"
        
        # STEP 7: Format response
        response = PredictionResponse(
            success=True,
            start_date=request.start_date,
            predicted_cycle_length=round(predicted_days, 1),
            next_period_date=next_date.strftime('%Y-%m-%d'),
            error_margin=ERROR_MARGIN,
            confidence_level=confidence,
            message=f"Prediksi: {next_date.strftime('%d %B %Y')} (rentang {min_date.strftime('%d %B')} - {max_date.strftime('%d %B %Y')})"
        )
        
        print(f"📤 Response: predicted_cycle_length = {response.predicted_cycle_length} hari")
        print(f"📤 Next period date = {response.next_period_date}")
        print("✅ PREDIKSI BERHASIL\n")
        
        return response
    
    except HTTPException:
        raise
    except Exception as e:
        print(f"❌ ERROR: {str(e)}")
        raise HTTPException(status_code=500, detail=f"Internal server error: {str(e)}")

# 7. ENDPOINT LAINNYA

@app.get("/api/health")
async def health_check():
    """Cek status kesehatan service"""
    return {
        "status": "healthy",
        "model_loaded": model is not None,
        "model_type": type(model).__name__ if model else None,
        "error_margin_mae": ERROR_MARGIN,
        "min_cycle_days": MIN_CYCLE_DAYS,
        "max_cycle_days": MAX_CYCLE_DAYS,
        "timestamp": datetime.now().isoformat()
    }


@app.get("/api/info")
async def get_info():
    """Informasi detail tentang model"""
    return {
        "model_type": type(model).__name__ if model else "Not loaded",
        "error_margin_mae": ERROR_MARGIN,
        "min_cycle_days": MIN_CYCLE_DAYS,
        "max_cycle_days": MAX_CYCLE_DAYS,
        "features_count": len(feature_names) if feature_names else 0,
        "features": feature_names if feature_names else [],
        "scaler_loaded": scaler is not None
    }


@app.get("/api/test")
async def test_prediction():
    """Endpoint untuk testing dengan data default"""
    test_request = PredictionRequest(
        start_date="2024-05-01",
        user_data=UserData(
            prev_cycle_length=28,
            pain_level=5,
            stress_score_cycle=4,
            sleep_hours_cycle=7,
            mood_score=7,
            age=25,
            bmi=22,
            pcos_diagnosed=0,
            birth_control_use=0
        )
    )
    return await predict(test_request)

# 8. RUN SERVER

if __name__ == "__main__":
    print("\n" + "="*60)
    print("🩸 MENSTRUAL CYCLE PREDICTION SERVICE v2.0")
    print("="*60)
    print("📌 LOGIKA UTAMA:")
    print("   1. AI menerima 9 fitur (prev_cycle_length, pain_level, dll)")
    print("   2. prev_cycle_length dihitung dari selisih tanggal")
    print("   3. AI memprediksi cycle_length_days (panjang siklus)")
    print("="*60)
    print(f"📊 Error Margin (MAE): {ERROR_MARGIN} hari")
    print(f"📁 Model path: data_ready/best_model.pkl")
    print(f"🌐 Server akan berjalan di: http://localhost:8001")
    print(f"📚 Dokumentasi API: http://localhost:8001/docs")
    print(f"🔄 Health check: http://localhost:8001/api/health")
    print(f"🧪 Test endpoint: http://localhost:8001/api/test")
    print("="*60 + "\n")
    
    uvicorn.run(
        app, 
        host="0.0.0.0", 
        port=8001,  # Gunakan port 8001 biar tidak bentrok dengan Laravel (yang pakai 8000)
        log_level="info"
    )