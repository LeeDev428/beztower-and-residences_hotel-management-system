<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Bez Tower & Residences</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f8f8f8, #ffffff);
            color: #2c2c2c;
            padding: 2rem;
        }

        .container {
            max-width: 1000px;
            margin: 0 auto;
        }

        .payment-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .payment-header {
            background: linear-gradient(135deg, #2c2c2c, #1a1a1a);
            color: white;
            padding: 2.5rem;
            text-align: center;
        }

        .payment-header h1 {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .gold-text {
            color: #d4af37;
        }

        .booking-ref-display {
            font-size: 1rem;
            margin-top: 1rem;
            padding: 0.8rem 1.5rem;
            background: rgba(212, 175, 55, 0.2);
            border-radius: 8px;
            display: inline-block;
        }

        .payment-body {
            padding: 3rem;
        }

        .alert {
            padding: 1.2rem 1.5rem;
            border-radius: 8px;
            margin-bottom: 2rem;
            display: flex;
            align-items: flex-start;
            gap: 1rem;
        }

        .alert-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .two-column-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin-top: 2rem;
        }

        .payment-instructions {
            background: #f8f8f8;
            padding: 2rem;
            border-radius: 10px;
        }

        .section-title {
            font-size: 1.5rem;
            color: #2c2c2c;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }

        .section-title i {
            color: #d4af37;
        }

        .payment-summary {
            background: linear-gradient(135deg, #fff9e6, #ffffff);
            padding: 1.5rem;
            border-radius: 10px;
            border: 2px solid #d4af37;
            margin-bottom: 1.5rem;
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #e5e5e5;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .down-payment-row {
            font-size: 1.4rem;
            font-weight: 700;
            color: #d4af37;
            padding-top: 1rem;
            margin-top: 0.5rem;
            border-top: 2px solid #d4af37;
        }

        .qr-code-container {
            text-align: center;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            border: 2px solid #e5e5e5;
        }

        .qr-code-container h3 {
            margin-bottom: 1.5rem;
            color: #2c2c2c;
        }

        .qr-code-wrapper {
            background: white;
            padding: 1.5rem;
            border-radius: 10px;
            display: inline-block;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .qr-code-wrapper img {
            width: 250px;
            height: 250px;
            object-fit: contain;
        }

        .gcash-info {
            margin-top: 1.5rem;
            padding: 1rem;
            background: #f8f8f8;
            border-radius: 8px;
        }

        .gcash-info p {
            margin: 0.5rem 0;
            color: #666;
        }

        .gcash-info strong {
            color: #2c2c2c;
        }

        .instructions-list {
            list-style: none;
            padding: 0;
        }

        .instructions-list li {
            padding: 0.8rem 0;
            padding-left: 2rem;
            position: relative;
            line-height: 1.6;
            color: #555;
        }

        .instructions-list li::before {
            content: attr(data-step);
            position: absolute;
            left: 0;
            top: 0.8rem;
            width: 24px;
            height: 24px;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 0.85rem;
        }

        .payment-form {
            margin-top: 2rem;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            border: 2px solid #e5e5e5;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c2c2c;
            font-weight: 600;
            font-size: 0.95rem;
        }

        .required {
            color: #d4af37;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 0.9rem 1.2rem;
            border: 2px solid #e5e5e5;
            border-radius: 8px;
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.3s;
        }

        .form-input:focus,
        .form-select:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload input[type="file"] {
            position: absolute;
            opacity: 0;
            width: 100%;
            height: 100%;
            cursor: pointer;
        }

        .file-upload-label {
            display: block;
            padding: 1rem;
            background: #f8f8f8;
            border: 2px dashed #d4af37;
            border-radius: 8px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .file-upload-label:hover {
            background: #fff9e6;
            border-color: #d4af37;
        }

        .file-upload-label i {
            font-size: 2rem;
            color: #d4af37;
            display: block;
            margin-bottom: 0.5rem;
        }

        .file-preview {
            margin-top: 1rem;
            display: none;
        }

        .file-preview img {
            max-width: 100%;
            max-height: 300px;
            border-radius: 8px;
            border: 2px solid #e5e5e5;
        }

        .submit-btn {
            width: 100%;
            background: linear-gradient(135deg, #d4af37, #f4e4c1);
            color: #2c2c2c;
            border: none;
            padding: 1.3rem 2rem;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            margin-top: 1rem;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .deadline-notice {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 1.2rem;
            margin-top: 1.5rem;
            border-radius: 5px;
        }

        .deadline-notice strong {
            color: #856404;
            display: block;
            margin-bottom: 0.5rem;
        }

        .deadline-notice p {
            color: #856404;
            margin: 0;
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }

            .payment-body {
                padding: 2rem 1.5rem;
            }

            .two-column-layout {
                grid-template-columns: 1fr;
                gap: 2rem;
            }

            .qr-code-wrapper img {
                width: 200px;
                height: 200px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="payment-card">
            <div class="payment-header">
                <h1>Complete Your <span class="gold-text">Payment</span></h1>
                <p>30% Down Payment Required</p>
                <div class="booking-ref-display">
                    <i class="fas fa-barcode"></i> {{ $booking->booking_reference }}
                </div>
            </div>

            <div class="payment-body">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <div>
                        <strong>Payment Instructions</strong>
                        <p>Please complete the 30% down payment within 48 hours to confirm your booking. Your reservation will be cancelled if payment is not received within the deadline.</p>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    <h3 style="margin-bottom: 1rem; color: #2c2c2c;">
                        <i class="fas fa-receipt"></i> Payment Breakdown
                    </h3>
                    <div class="summary-row">
                        <span>Room: {{ $booking->room->roomType->name }}</span>
                        <span>₱{{ number_format($booking->room->roomType->base_price, 2) }}/night</span>
                    </div>
                    <div class="summary-row">
                        <span>Number of Nights</span>
                        <span>{{ $booking->total_nights }} {{ $booking->total_nights == 1 ? 'night' : 'nights' }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>₱{{ number_format($booking->subtotal, 2) }}</span>
                    </div>
                    @if($booking->extras_total > 0)
                        <div class="summary-row">
                            <span>Additional Services</span>
                            <span>₱{{ number_format($booking->extras_total, 2) }}</span>
                        </div>
                    @endif
                    <div class="summary-row">
                        <span>Tax (12%)</span>
                        <span>₱{{ number_format($booking->tax_amount, 2) }}</span>
                    </div>
                    <div class="summary-row">
                        <span>Total Amount</span>
                        <span>₱{{ number_format($booking->total_amount, 2) }}</span>
                    </div>
                    <div class="summary-row down-payment-row">
                        <span>Down Payment (30%)</span>
                        <span>₱{{ number_format($downPaymentAmount, 2) }}</span>
                    </div>
                </div>

                <!-- Two Column Layout -->
                <div class="two-column-layout">
                    <!-- Left Column: QR Code -->
                    <div>
                        <div class="section-title">
                            <i class="fas fa-qrcode"></i> GCash Payment
                        </div>
                        
                        <div class="qr-code-container">
                            <h3>Scan to Pay</h3>
                            <div class="qr-code-wrapper">
                                <!-- Replace with your actual GCash QR code image -->
                                <img src="{{ asset('images/gcash-qr-code.png') }}" alt="GCash QR Code" 
                                     onerror="this.src='https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=09123456789'">
                            </div>
                            <div class="gcash-info">
                                <p><strong>Account Name:</strong> Bez Tower & Residences</p>
                                <p><strong>Mobile Number:</strong> +63 912 345 6789</p>
                                <p><strong>Amount:</strong> ₱{{ number_format($downPaymentAmount, 2) }}</p>
                            </div>
                        </div>

                        <div class="deadline-notice">
                            <strong><i class="fas fa-clock"></i> Payment Deadline</strong>
                            <p>Payment must be made within 48 hours or booking will be cancelled.</p>
                        </div>
                    </div>

                    <!-- Right Column: Payment Instructions & Upload Form -->
                    <div>
                        <div class="payment-instructions">
                            <div class="section-title">
                                <i class="fas fa-list-ol"></i> How to Pay
                            </div>
                            
                            <ol class="instructions-list">
                                <li data-step="1">Open your GCash app on your mobile phone</li>
                                <li data-step="2">Tap "Scan QR" and scan the QR code above</li>
                                <li data-step="3">Confirm the amount (₱{{ number_format($downPaymentAmount, 2) }})</li>
                                <li data-step="4">Complete the payment in your GCash app</li>
                                <li data-step="5">Take a screenshot of the payment confirmation</li>
                                <li data-step="6">Upload the screenshot below and submit</li>
                            </ol>
                        </div>

                        <!-- Payment Upload Form -->
                        <form action="{{ route('booking.processPayment', $booking->booking_reference) }}" 
                              method="POST" enctype="multipart/form-data" class="payment-form" id="paymentForm">
                            @csrf
                            
                            <h3 style="margin-bottom: 1.5rem; color: #2c2c2c;">
                                <i class="fas fa-upload"></i> Submit Payment Proof
                            </h3>

                            <div class="form-group">
                                <label class="form-label">Payment Method <span class="required">*</span></label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="gcash" selected>GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label class="form-label">GCash Reference Number <span class="required">*</span></label>
                                <input type="text" name="payment_reference" class="form-input" 
                                       placeholder="Enter 13-digit reference number" 
                                       pattern="[0-9]{13}" 
                                       title="Please enter a valid 13-digit reference number"
                                       required>
                                <small style="color: #666; font-size: 0.85rem;">
                                    <i class="fas fa-info-circle"></i> Found in your GCash transaction details
                                </small>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Upload Payment Screenshot <span class="required">*</span></label>
                                <div class="file-upload">
                                    <input type="file" name="proof_of_payment" id="proofUpload" 
                                           accept="image/jpeg,image/png,image/jpg" 
                                           required onchange="previewImage(this)">
                                    <label for="proofUpload" class="file-upload-label" id="fileLabel">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p style="margin: 0; color: #666;">Click to upload or drag and drop</p>
                                        <small style="color: #999;">PNG, JPG (Max 5MB)</small>
                                    </label>
                                </div>
                                <div class="file-preview" id="imagePreview">
                                    <img src="" alt="Preview" id="previewImg">
                                </div>
                                @error('proof_of_payment')
                                    <small style="color: #dc3545;">{{ $message }}</small>
                                @enderror
                            </div>

                            <button type="submit" class="submit-btn" id="submitBtn">
                                <i class="fas fa-paper-plane"></i> Submit Payment Proof
                            </button>

                            <p style="text-align: center; margin-top: 1rem; color: #666; font-size: 0.9rem;">
                                <i class="fas fa-shield-alt"></i> Your payment will be verified within 24-48 hours
                            </p>
                        </form>
                    </div>
                </div>

                <!-- Hotel Contact -->
                <div style="margin-top: 3rem; padding: 1.5rem; background: #f8f8f8; border-radius: 8px;">
                    <h3 style="margin-bottom: 1rem; color: #2c2c2c;">
                        <i class="fas fa-headset" style="color: #d4af37;"></i> Need Help?
                    </h3>
                    <p style="color: #666; line-height: 1.8;">
                        If you encounter any issues with your payment, please contact us:<br>
                        <strong>Phone:</strong> +1 234 567 8910<br>
                        <strong>Email:</strong> reservations@beztower.com<br>
                        <strong>Hours:</strong> 24/7 Customer Support
                    </p>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const fileLabel = document.getElementById('fileLabel');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    fileLabel.innerHTML = '<i class="fas fa-check-circle" style="color: #28a745;"></i><p style="margin: 0.5rem 0; color: #28a745;">File uploaded successfully</p><small style="color: #666;">' + input.files[0].name + '</small>';
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';
        });
    </script>
</body>
</html>
