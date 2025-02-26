@extends('layouts.site')

@section('title', 'Payment')

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg rounded-3">
                    <div class="card-header bg-sky-600 text-white py-3 rounded-top">
                        <h4 class="mb-0">Secure Payment</h4>
                    </div>
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <h5 class="mb-1">Order Summary</h5>
                            <p class="text-muted">You are paying for the <b class="text-danger">{{ ucfirst($plan) }}</b> plan.</p>
                        </div>

                        <hr class="my-4">

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="fw-bold">Plan Cost:</span>
                            <span class="text-danger">€<b>{{ number_format($price, 2) }}</b></span>
                        </div>

                        <hr class="my-4">

                        <div class="text-center">
                            <p class="mb-3">Scan the QR code below to complete your secure payment:</p>
                            <img class="img-fluid qr-code" src="{{ asset('images/QRCODE.jpg') }}" alt="content-image">
                        </div>
                    </div>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="text-muted small">
                            Thank you for your business! Your payment is processed securely.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


