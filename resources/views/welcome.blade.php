{{-- ===================== WELCOME / INDEX PAGE ===================== --}}
@extends('layouts.app')

@section('title', 'Mirai - AI-Powered Cycle Predictor')

@section('content')

    {{-- Hero Section --}}
    @include('sections.hero')

    {{-- About Section --}}
    @include('sections.about')

    {{-- CTA Section --}}
    @include('sections.cta')

    {{-- FAQ Section --}}
    @include('sections.faq')

    {{-- Team Section --}}
    @include('sections.team')

    {{-- Contact Section --}}
    @include('sections.contact')

@endsection