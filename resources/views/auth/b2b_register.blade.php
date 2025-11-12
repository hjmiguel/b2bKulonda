@extends("backend.layouts.blank")

@section("content")
<style>
:root {
    --primary-color: #007bff;
    --primary-hover: #0056b3;
    --success-color: #28a745;
    --danger-color: #dc3545;
    --text-dark: #333;
    --text-muted: #6c757d;
    --border-color: #dee2e6;
    --bg-light: #f8f9fa;
    --bg-field: #e7f1ff;
}

.b2b-registration-container {
    min-height: 100vh;
    background: var(--bg-light);
    padding: 40px 20px;
}

.registration-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    max-width: 1100px;
    margin: 0 auto;
    padding: 40px 50px;
}

.registration-title {
    font-size: 32px;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 40px;
}

/* Step Indicator - Horizontal Circles */
.step-indicator {
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: relative;
    margin-bottom: 50px;
    max-width: 900px;
    margin-left: auto;
    margin-right: auto;
}

.step-indicator::before {
    content: '';
    position: absolute;
    top: 25px;
    left: 50px;
    right: 50px;
    height: 2px;
    background: var(--border-color);
    z-index: 0;
}

.step-progress-bar {
    position: absolute;
    top: 25px;
    left: 50px;
    height: 2px;
    background: var(--primary-color);
    transition: width 0.4s ease;
    z-index: 1;
}

.step-item {
    position: relative;
    z-index: 2;
    text-align: center;
    flex: 1;
}

.step-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    background: white;
    border: 2px solid var(--border-color);
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 12px;
    font-weight: 600;
    font-size: 18px;
    color: var(--text-muted);
    transition: all 0.3s;
}

.step-item.active .step-circle {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.step-item.completed .step-circle {
    background: var(--success-color);
    border-color: var(--success-color);
    color: white;
}

.step-item .step-circle i {
    font-size: 20px;
}

.step-label {
    font-size: 14px;
    color: var(--text-muted);
    font-weight: 500;
}

.step-item.active .step-label {
    color: var(--primary-color);
    font-weight: 600;
}

/* Form Content */
.form-content {
    padding: 20px 0;
}

.form-step {
    display: none;
}

.form-step.active {
    display: block;
    animation: fadeIn 0.3s ease;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-title {
    font-size: 26px;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 30px;
}

/* Form Groups */
.form-group {
    margin-bottom: 24px;
}

.form-group label {
    font-weight: 500;
    color: var(--text-dark);
    margin-bottom: 8px;
    display: block;
    font-size: 14px;
}

.form-group label .text-danger {
    color: var(--danger-color);
    margin-left: 3px;
}

.form-control {
    padding: 12px 16px;
    font-size: 15px;
    border: 1px solid var(--border-color);
    border-radius: 4px;
    transition: all 0.2s;
    background: white;
}

.form-control:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
    background: var(--bg-field);
    outline: none;
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.invalid-feedback {
    display: block;
    font-size: 13px;
    color: var(--danger-color);
    margin-top: 5px;
}

/* File Upload */
.file-upload-wrapper {
    border: 2px dashed var(--border-color);
    padding: 30px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
    background: #fafafa;
    border-radius: 6px;
}

.file-upload-wrapper:hover {
    border-color: var(--primary-color);
    background: var(--bg-field);
}

.file-upload-wrapper.has-file {
    border-color: var(--success-color);
    border-style: solid;
    background: #f0f9f4;
}

.file-upload-wrapper i {
    font-size: 40px;
    color: var(--text-muted);
    margin-bottom: 10px;
}

.file-upload-wrapper.has-file i {
    color: var(--success-color);
}

.file-info {
    display: none;
    margin-top: 15px;
}

.file-upload-wrapper.has-file .file-info {
    display: block;
}

.file-upload-wrapper.has-file .file-prompt {
    display: none;
}

.btn-remove-file {
    margin-top: 10px;
    font-size: 13px;
    color: var(--danger-color);
    background: none;
    border: 1px solid var(--danger-color);
    padding: 5px 15px;
    cursor: pointer;
    border-radius: 4px;
    transition: all 0.3s;
}

.btn-remove-file:hover {
    background: var(--danger-color);
    color: white;
}

/* Review Section */
.review-section {
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--border-color);
}

.review-section:last-child {
    border-bottom: none;
}

.review-section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
}

.review-section-title {
    font-size: 18px;
    font-weight: 600;
    color: var(--text-dark);
}

.btn-edit {
    font-size: 14px;
    color: var(--primary-color);
    text-decoration: none;
    transition: all 0.2s;
}

.btn-edit:hover {
    color: var(--primary-hover);
    text-decoration: underline;
}

.review-item {
    display: flex;
    padding: 8px 0;
}

.review-label {
    flex: 0 0 35%;
    font-weight: 500;
    color: var(--text-muted);
    font-size: 14px;
}

.review-value {
    flex: 1;
    color: var(--text-dark);
    font-size: 14px;
}

/* Checkbox */
.custom-control-label {
    font-weight: 400;
    font-size: 14px;
    cursor: pointer;
}

.custom-checkbox .custom-control-input:checked ~ .custom-control-label::before {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

/* Navigation Buttons */
.form-navigation {
    margin-top: 40px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.btn-prev, .btn-next, .btn-submit {
    padding: 12px 32px;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    transition: all 0.3s;
    border: none;
    cursor: pointer;
}

.btn-prev {
    background: white;
    border: 2px solid var(--border-color);
    color: var(--text-dark);
}

.btn-prev:hover {
    background: var(--bg-light);
    border-color: var(--text-muted);
}

.btn-next, .btn-submit {
    background: var(--primary-color);
    color: white;
}

.btn-next:hover, .btn-submit:hover {
    background: var(--primary-hover);
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.btn-next:disabled, .btn-submit:disabled {
    opacity: 0.5;
    cursor: not-allowed;
    transform: none;
}

/* Alert Box */
.alert-info {
    background: #d1ecf1;
    border: 1px solid #bee5eb;
    border-left: 4px solid #17a2b8;
    padding: 15px 20px;
    margin-bottom: 25px;
    font-size: 14px;
    border-radius: 4px;
    color: #0c5460;
}

.alert-warning {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-left: 4px solid #ffc107;
    padding: 15px 20px;
    margin-bottom: 25px;
    font-size: 14px;
    border-radius: 4px;
    color: #856404;
}

/* Badge */
.badge-sector {
    display: inline-block;
    background: #e7f1ff;
    color: var(--primary-color);
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 500;
    margin-bottom: 30px;
}

/* Responsive */
@media (max-width: 768px) {
    .registration-card {
        padding: 30px 20px;
    }

    .step-indicator::before {
        left: 30px;
        right: 30px;
    }

    .step-progress-bar {
        left: 30px;
    }

    .step-circle {
        width: 40px;
        height: 40px;
        font-size: 16px;
    }

    .step-label {
        font-size: 12px;
    }

    .review-item {
        flex-direction: column;
    }

    .review-label {
        margin-bottom: 5px;
    }
}
</style>

<div class="b2b-registration-container">
    <div class="container">
        <div class="registration-card">
            <!-- Title -->
            <h1 class="registration-title">{{ translate('B2B Business Registration') }}</h1>

            <div class="badge-sector">
                <i class="las la-utensils"></i> {{ translate('Food Sector Only') }}
            </div>

            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-progress-bar" id="progressBar" style="width: 0%"></div>

                <div class="step-item active" data-step="1">
                    <div class="step-circle">1</div>
                    <div class="step-label">Personal</div>
                </div>

                <div class="step-item" data-step="2">
                    <div class="step-circle">2</div>
                    <div class="step-label">Company</div>
                </div>

                <div class="step-item" data-step="3">
                    <div class="step-circle">3</div>
                    <div class="step-label">Address</div>
                </div>

                <div class="step-item" data-step="4">
                    <div class="step-circle">4</div>
                    <div class="step-label">Business</div>
                </div>

                <div class="step-item" data-step="5">
                    <div class="step-circle">5</div>
                    <div class="step-label">Documents</div>
                </div>

                <div class="step-item" data-step="6">
                    <div class="step-circle"><i class="las la-check"></i></div>
                    <div class="step-label">Review</div>
                </div>
            </div>

            <!-- Form -->
            <form action="{{ route('buyer.register.submit') }}" method="POST" id="b2bRegistrationForm" enctype="multipart/form-data">
                @csrf

                <!-- Step 1: Personal Information -->
                <div class="form-step active" data-step="1">
                    <div class="form-content">
                        <h3 class="step-title">{{ translate('Personal Information') }}</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="full_name">{{ translate('Full Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name"
                                           value="{{ old('full_name') }}" placeholder="Full Name" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">{{ translate('Email') }} <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email') }}" placeholder="customer@example.com" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">{{ translate('Phone') }} <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control phone-mask" id="phone" name="phone"
                                           value="{{ old('phone') }}" placeholder="+244 9XX XXX XXX" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="role">{{ translate('Position/Role') }}</label>
                                    <input type="text" class="form-control" id="role" name="role"
                                           value="{{ old('role') }}" placeholder="Purchasing Manager">
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Company -->
                <div class="form-step" data-step="2">
                    <div class="form-content">
                        <h3 class="step-title">{{ translate('Company Information') }}</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_name">{{ translate('Company Name') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="company_name" name="company_name"
                                           value="{{ old('company_name') }}" required>
                                    @error('company_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="trade_name">{{ translate('Trade Name') }}</label>
                                    <input type="text" class="form-control" id="trade_name" name="trade_name"
                                           value="{{ old('trade_name') }}">
                                    @error('trade_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="nif">{{ translate('NIF') }}</label>
                                    <input type="text" class="form-control" id="nif" name="nif"
                                           value="{{ old('nif') }}" placeholder="9-10 digits" maxlength="10">
                                    @error('nif')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="registration_number">{{ translate('Registration Number') }}</label>
                                    <input type="text" class="form-control" id="registration_number" name="registration_number"
                                           value="{{ old('registration_number') }}">
                                    @error('registration_number')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_type">{{ translate('Company Type') }}</label>
                                    <select class="form-control" id="company_type" name="company_type">
                                        <option value="">{{ translate('Select') }}</option>
                                        @foreach($config['company_types'] as $key => $value)
                                            <option value="{{ $key }}" {{ old('company_type') == $key ? 'selected' : '' }}>
                                                {{ translate($value) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('company_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="founded_at">{{ translate('Founded Date') }}</label>
                                    <input type="date" class="form-control" id="founded_at" name="founded_at"
                                           value="{{ old('founded_at') }}">
                                    @error('founded_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="website">{{ translate('Website') }}</label>
                                    <input type="url" class="form-control" id="website" name="website"
                                           value="{{ old('website') }}" placeholder="https://www.company.ao">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 3: Address -->
                <div class="form-step" data-step="3">
                    <div class="form-content">
                        <h3 class="step-title">{{ translate('Address & Contact') }}</h3>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="address_line">{{ translate('Complete Address') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="address_line" name="address_line"
                                           value="{{ old('address_line') }}" placeholder="Street, Number, District, Municipality" required>
                                    @error('address_line')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city">{{ translate('Province') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="city" name="city" required>
                                        <option value="">{{ translate('Select') }}</option>
                                        @foreach($config['cities'] as $city)
                                            <option value="{{ $city }}" {{ old('city') == $city ? 'selected' : '' }}>
                                                {{ $city }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="postal_code">{{ translate('Postal Code') }}</label>
                                    <input type="text" class="form-control" id="postal_code" name="postal_code"
                                           value="{{ old('postal_code', '—') }}" placeholder="—">
                                    @error('postal_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_phone">{{ translate('Company Phone') }}</label>
                                    <input type="tel" class="form-control phone-mask" id="company_phone" name="company_phone"
                                           value="{{ old('company_phone') }}" placeholder="+244 9XX XXX XXX">
                                    @error('company_phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="company_email">{{ translate('Company Email') }}</label>
                                    <input type="email" class="form-control" id="company_email" name="company_email"
                                           value="{{ old('company_email') }}" placeholder="contact@company.ao">
                                    @error('company_email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 4: Business Profile -->
                <div class="form-step" data-step="4">
                    <div class="form-content">
                        <h3 class="step-title">{{ translate('Business Profile & Segment') }}</h3>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="industry">{{ translate('Business Category') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="industry" name="industry" required>
                                        <option value="">{{ translate('Select category') }}</option>
                                        @foreach($config['categories'] as $category => $types)
                                            <option value="{{ $category }}" {{ old('industry') == $category ? 'selected' : '' }}>
                                                {{ translate($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('industry')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="business_type">{{ translate('Business Type') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="business_type" name="business_type" required disabled>
                                        <option value="">{{ translate('Select category first') }}</option>
                                    </select>
                                    @error('business_type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="annual_revenue_range">{{ translate('Annual Revenue (Kz)') }}</label>
                                    <select class="form-control" id="annual_revenue_range" name="annual_revenue_range">
                                        <option value="">{{ translate('Select') }}</option>
                                        @foreach($config['revenue_ranges'] as $key => $value)
                                            <option value="{{ $key }}" {{ old('annual_revenue_range') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('annual_revenue_range')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="employees_range">{{ translate('Number of Employees') }}</label>
                                    <select class="form-control" id="employees_range" name="employees_range">
                                        <option value="">{{ translate('Select') }}</option>
                                        @foreach($config['employees_ranges'] as $key => $value)
                                            <option value="{{ $key }}" {{ old('employees_range') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('employees_range')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="est_monthly_purchases">{{ translate('Estimated Monthly Purchases (Kz)') }}</label>
                                    <input type="number" class="form-control" id="est_monthly_purchases"
                                           name="est_monthly_purchases" value="{{ old('est_monthly_purchases') }}"
                                           min="0" step="0.01" placeholder="500000">
                                    @error('est_monthly_purchases')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="credit_limit_requested">{{ translate('Requested Credit Limit (Kz)') }}</label>
                                    <input type="number" class="form-control" id="credit_limit_requested"
                                           name="credit_limit_requested" value="{{ old('credit_limit_requested') }}"
                                           min="0" step="0.01" placeholder="1000000">
                                    @error('credit_limit_requested')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="payment_terms_days">{{ translate('Payment Terms') }}</label>
                                    <select class="form-control" id="payment_terms_days" name="payment_terms_days">
                                        <option value="">{{ translate('Select') }}</option>
                                        @foreach($config['payment_terms'] as $key => $value)
                                            <option value="{{ $key }}" {{ old('payment_terms_days') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('payment_terms_days')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="bank">{{ translate('Main Bank') }}</label>
                                    <select class="form-control" id="bank" name="bank">
                                        <option value="">{{ translate('Select') }}</option>
                                        @foreach($config['banks'] as $key => $value)
                                            <option value="{{ $key }}" {{ old('bank') == $key ? 'selected' : '' }}>
                                                {{ $value }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('bank')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="has_insurance"
                                               name="has_insurance" value="1" {{ old('has_insurance') ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="has_insurance">
                                            {{ translate('Has credit insurance?') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6" id="insurer_wrapper" style="display: none;">
                                <div class="form-group">
                                    <label for="insurer">{{ translate('Insurance Company') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="insurer" name="insurer"
                                           value="{{ old('insurer') }}" placeholder="ENSA, AAA Seguros">
                                    @error('insurer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Documents -->
                <div class="form-step" data-step="5">
                    <div class="form-content">
                        <h3 class="step-title">{{ translate('Required Documents') }}</h3>

                        <div class="alert-info">
                            <i class="las la-info-circle"></i>
                            {{ translate('Accepted formats: PDF, JPG, PNG, DOC, DOCX. Maximum size: 5MB per file.') }}
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <label>{{ translate('Commercial Certificate') }}</label>
                                <div class="file-upload-wrapper" onclick="document.getElementById('doc_commercial_cert').click()">
                                    <input type="file" id="doc_commercial_cert" name="doc_commercial_cert"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                                    <div class="file-prompt">
                                        <i class="las la-cloud-upload-alt"></i>
                                        <p style="margin: 10px 0 0; font-weight: 500;">{{ translate('Click to select file') }}</p>
                                    </div>
                                    <div class="file-info">
                                        <i class="las la-file-check"></i>
                                        <div class="file-name"></div>
                                        <div class="file-size"></div>
                                        <button type="button" class="btn-remove-file" onclick="removeFile(event, 'doc_commercial_cert')">
                                            {{ translate('Remove') }}
                                        </button>
                                    </div>
                                </div>
                                @error('doc_commercial_cert')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label>{{ translate('NIF Declaration') }}</label>
                                <div class="file-upload-wrapper" onclick="document.getElementById('doc_nif').click()">
                                    <input type="file" id="doc_nif" name="doc_nif"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                                    <div class="file-prompt">
                                        <i class="las la-cloud-upload-alt"></i>
                                        <p style="margin: 10px 0 0; font-weight: 500;">{{ translate('Click to select file') }}</p>
                                    </div>
                                    <div class="file-info">
                                        <i class="las la-file-check"></i>
                                        <div class="file-name"></div>
                                        <div class="file-size"></div>
                                        <button type="button" class="btn-remove-file" onclick="removeFile(event, 'doc_nif')">
                                            {{ translate('Remove') }}
                                        </button>
                                    </div>
                                </div>
                                @error('doc_nif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label>{{ translate('ID Card (Legal Representative)') }}</label>
                                <div class="file-upload-wrapper" onclick="document.getElementById('doc_id').click()">
                                    <input type="file" id="doc_id" name="doc_id"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                                    <div class="file-prompt">
                                        <i class="las la-cloud-upload-alt"></i>
                                        <p style="margin: 10px 0 0; font-weight: 500;">{{ translate('Click to select file') }}</p>
                                    </div>
                                    <div class="file-info">
                                        <i class="las la-file-check"></i>
                                        <div class="file-name"></div>
                                        <div class="file-size"></div>
                                        <button type="button" class="btn-remove-file" onclick="removeFile(event, 'doc_id')">
                                            {{ translate('Remove') }}
                                        </button>
                                    </div>
                                </div>
                                @error('doc_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-4">
                                <label>{{ translate('Proof of Address') }}</label>
                                <div class="file-upload-wrapper" onclick="document.getElementById('doc_address_proof').click()">
                                    <input type="file" id="doc_address_proof" name="doc_address_proof"
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                                    <div class="file-prompt">
                                        <i class="las la-cloud-upload-alt"></i>
                                        <p style="margin: 10px 0 0; font-weight: 500;">{{ translate('Click to select file') }}</p>
                                    </div>
                                    <div class="file-info">
                                        <i class="las la-file-check"></i>
                                        <div class="file-name"></div>
                                        <div class="file-size"></div>
                                        <button type="button" class="btn-remove-file" onclick="removeFile(event, 'doc_address_proof')">
                                            {{ translate('Remove') }}
                                        </button>
                                    </div>
                                </div>
                                @error('doc_address_proof')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-4">
                                <label>{{ translate('Additional Documents (Optional)') }}</label>
                                <div class="file-upload-wrapper" onclick="document.getElementById('doc_other').click()">
                                    <input type="file" id="doc_other" name="doc_other[]" multiple
                                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" style="display: none;">
                                    <div class="file-prompt">
                                        <i class="las la-cloud-upload-alt"></i>
                                        <p style="margin: 10px 0 0; font-weight: 500;">{{ translate('Click to select multiple files') }}</p>
                                    </div>
                                    <div class="file-info">
                                        <i class="las la-file-check"></i>
                                        <div class="file-name"></div>
                                        <div class="file-size"></div>
                                        <button type="button" class="btn-remove-file" onclick="removeFile(event, 'doc_other')">
                                            {{ translate('Remove') }}
                                        </button>
                                    </div>
                                </div>
                                @error('doc_other')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Review -->
                <div class="form-step" data-step="6">
                    <div class="form-content">
                        <h3 class="step-title">{{ translate('Review & Confirm') }}</h3>

                        <!-- Review Section 1 -->
                        <div class="review-section">
                            <div class="review-section-header">
                                <h4 class="review-section-title">{{ translate('Personal Information') }}</h4>
                                <a href="#" class="btn-edit" onclick="goToStep(1); return false;">{{ translate('Edit') }}</a>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Name') }}:</div>
                                <div class="review-value" id="review_full_name">—</div>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Email') }}:</div>
                                <div class="review-value" id="review_email">—</div>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Phone') }}:</div>
                                <div class="review-value" id="review_phone">—</div>
                            </div>
                        </div>

                        <!-- Review Section 2 -->
                        <div class="review-section">
                            <div class="review-section-header">
                                <h4 class="review-section-title">{{ translate('Company') }}</h4>
                                <a href="#" class="btn-edit" onclick="goToStep(2); return false;">{{ translate('Edit') }}</a>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Company Name') }}:</div>
                                <div class="review-value" id="review_company_name">—</div>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('NIF') }}:</div>
                                <div class="review-value" id="review_nif">—</div>
                            </div>
                        </div>

                        <!-- Review Section 3 -->
                        <div class="review-section">
                            <div class="review-section-header">
                                <h4 class="review-section-title">{{ translate('Address') }}</h4>
                                <a href="#" class="btn-edit" onclick="goToStep(3); return false;">{{ translate('Edit') }}</a>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Address') }}:</div>
                                <div class="review-value" id="review_address_line">—</div>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Province') }}:</div>
                                <div class="review-value" id="review_city">—</div>
                            </div>
                        </div>

                        <!-- Review Section 4 -->
                        <div class="review-section">
                            <div class="review-section-header">
                                <h4 class="review-section-title">{{ translate('Business Profile') }}</h4>
                                <a href="#" class="btn-edit" onclick="goToStep(4); return false;">{{ translate('Edit') }}</a>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Category') }}:</div>
                                <div class="review-value" id="review_industry">—</div>
                            </div>
                            <div class="review-item">
                                <div class="review-label">{{ translate('Business Type') }}:</div>
                                <div class="review-value" id="review_business_type">—</div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="form-group mt-4">
                            <label for="notes">{{ translate('Additional Notes (Optional)') }}</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"
                                      placeholder="{{ translate('Any additional information...') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Declarations -->
                        <div class="alert-warning mt-4">
                            <div class="custom-control custom-checkbox mb-3">
                                <input type="checkbox" class="custom-control-input" id="confirm_true"
                                       name="confirm_true" value="1" required>
                                <label class="custom-control-label" for="confirm_true">
                                    <strong>{{ translate('I declare that all information provided is true and correct.') }}</strong>
                                </label>
                            </div>

                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="accept_terms"
                                       name="accept_terms" value="1" required>
                                <label class="custom-control-label" for="accept_terms">
                                    {{ translate('I accept the') }}
                                    <a href="#" target="_blank">{{ translate('Terms and Conditions') }}</a> and
                                    <a href="#" target="_blank">{{ translate('Privacy Policy') }}</a>.
                                </label>
                            </div>
                        </div>

                        @if($errors->any())
                            <div class="alert alert-danger mt-3">
                                <strong>{{ translate('Errors found:') }}</strong>
                                <ul class="mb-0 mt-2">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="form-navigation">
                    <button type="button" class="btn-prev" id="btnPrev" onclick="previousStep()" style="display: none;">
                        <i class="las la-arrow-left"></i> {{ translate('Back') }}
                    </button>

                    <div style="flex: 1;"></div>

                    <button type="button" class="btn-next" id="btnNext" onclick="nextStep()">
                        {{ translate('Next') }} <i class="las la-arrow-right"></i>
                    </button>

                    <button type="submit" class="btn-submit" id="btnSubmit" style="display: none;">
                        {{ translate('Submit Registration') }} <i class="las la-check-circle"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const categoriesConfig = @json($config['categories']);
let currentStep = 1;
const totalSteps = 6;

document.addEventListener('DOMContentLoaded', function() {
    updateStepUI();
    setupPhoneMask();
    setupFileUploads();
    setupInsuranceToggle();
    setupCategoryChange();

    @if(old('industry'))
        const oldIndustry = "{{ old('industry') }}";
        const oldBusinessType = "{{ old('business_type') }}";
        if (oldIndustry) {
            updateBusinessTypes(oldIndustry, oldBusinessType);
        }
    @endif
});

function nextStep() {
    if (validateStep(currentStep)) {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepUI();
            updateReviewData();
            window.scrollTo(0, 0);
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        updateStepUI();
        window.scrollTo(0, 0);
    }
}

function goToStep(step) {
    if (step >= 1 && step <= totalSteps) {
        currentStep = step;
        updateStepUI();
        window.scrollTo(0, 0);
    }
}

function updateStepUI() {
    document.querySelectorAll('.form-step').forEach(step => {
        step.classList.remove('active');
    });

    const currentStepElement = document.querySelector(`.form-step[data-step="${currentStep}"]`);
    if (currentStepElement) {
        currentStepElement.classList.add('active');
    }

    document.querySelectorAll('.step-item').forEach((item, index) => {
        const stepNumber = index + 1;
        item.classList.remove('active', 'completed');

        if (stepNumber < currentStep) {
            item.classList.add('completed');
            item.querySelector('.step-circle').innerHTML = '<i class="las la-check"></i>';
        } else if (stepNumber === currentStep) {
            item.classList.add('active');
            item.querySelector('.step-circle').textContent = stepNumber;
        } else {
            item.querySelector('.step-circle').textContent = stepNumber;
        }
    });

    const progressPercent = ((currentStep - 1) / (totalSteps - 1)) * 100;
    document.getElementById('progressBar').style.width = progressPercent + '%';

    const btnPrev = document.getElementById('btnPrev');
    const btnNext = document.getElementById('btnNext');
    const btnSubmit = document.getElementById('btnSubmit');

    btnPrev.style.display = currentStep === 1 ? 'none' : 'inline-block';
    btnNext.style.display = currentStep === totalSteps ? 'none' : 'inline-block';
    btnSubmit.style.display = currentStep === totalSteps ? 'inline-block' : 'none';
}

function validateStep(step) {
    let isValid = true;
    const currentStepElement = document.querySelector(`.form-step[data-step="${step}"]`);

    if (!currentStepElement) return true;

    const requiredFields = currentStepElement.querySelectorAll('[required]');

    requiredFields.forEach(field => {
        if (!field.value || (field.type === 'checkbox' && !field.checked)) {
            isValid = false;
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
        }
    });

    if (!isValid) {
        alert('Please fill in all required fields.');
    }

    return isValid;
}

function updateReviewData() {
    if (currentStep === 6) {
        document.getElementById('review_full_name').textContent = document.getElementById('full_name').value || '—';
        document.getElementById('review_email').textContent = document.getElementById('email').value || '—';
        document.getElementById('review_phone').textContent = document.getElementById('phone').value || '—';
        document.getElementById('review_company_name').textContent = document.getElementById('company_name').value || '—';
        document.getElementById('review_nif').textContent = document.getElementById('nif').value || '—';
        document.getElementById('review_address_line').textContent = document.getElementById('address_line').value || '—';
        document.getElementById('review_city').textContent = document.getElementById('city').value || '—';
        document.getElementById('review_industry').textContent = document.getElementById('industry').value || '—';
        document.getElementById('review_business_type').textContent = document.getElementById('business_type').value || '—';
    }
}

function setupPhoneMask() {
    document.querySelectorAll('.phone-mask').forEach(input => {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length > 9) value = value.substring(0, 9);
            if (value.length > 6) {
                e.target.value = '+244 ' + value.substring(0, 3) + ' ' + value.substring(3, 6) + ' ' + value.substring(6);
            } else if (value.length > 3) {
                e.target.value = '+244 ' + value.substring(0, 3) + ' ' + value.substring(3);
            } else if (value.length > 0) {
                e.target.value = '+244 ' + value;
            } else {
                e.target.value = '';
            }
        });
    });
}

function setupFileUploads() {
    const fileInputs = ['doc_commercial_cert', 'doc_nif', 'doc_id', 'doc_address_proof', 'doc_other'];
    fileInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('change', function(e) {
                handleFileSelect(e, inputId);
            });
        }
    });
}

function handleFileSelect(event, inputId) {
    const files = event.target.files;
    const wrapper = event.target.closest('.file-upload-wrapper');

    if (files.length > 0) {
        const file = files[0];
        const maxSize = 5 * 1024 * 1024;

        if (file.size > maxSize) {
            alert('File too large. Maximum size: 5MB');
            event.target.value = '';
            return;
        }

        wrapper.classList.add('has-file');
        wrapper.querySelector('.file-name').textContent = file.name;
        wrapper.querySelector('.file-size').textContent = formatFileSize(file.size);

        if (inputId === 'doc_other' && files.length > 1) {
            wrapper.querySelector('.file-name').textContent = files.length + ' files selected';
            let totalSize = 0;
            for (let i = 0; i < files.length; i++) {
                totalSize += files[i].size;
            }
            wrapper.querySelector('.file-size').textContent = formatFileSize(totalSize);
        }
    }
}

function removeFile(event, inputId) {
    event.stopPropagation();
    const input = document.getElementById(inputId);
    const wrapper = input.closest('.file-upload-wrapper');
    input.value = '';
    wrapper.classList.remove('has-file');
}

function setupInsuranceToggle() {
    const checkbox = document.getElementById('has_insurance');
    const wrapper = document.getElementById('insurer_wrapper');
    const insurerInput = document.getElementById('insurer');

    if (checkbox) {
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                wrapper.style.display = 'block';
                insurerInput.setAttribute('required', 'required');
            } else {
                wrapper.style.display = 'none';
                insurerInput.removeAttribute('required');
                insurerInput.value = '';
            }
        });

        if (checkbox.checked) {
            wrapper.style.display = 'block';
            insurerInput.setAttribute('required', 'required');
        }
    }
}

function setupCategoryChange() {
    const industrySelect = document.getElementById('industry');
    if (industrySelect) {
        industrySelect.addEventListener('change', function() {
            updateBusinessTypes(this.value);
        });
    }
}

function updateBusinessTypes(category, selectedValue = '') {
    const businessTypeSelect = document.getElementById('business_type');
    if (!category) {
        businessTypeSelect.innerHTML = '<option value="">Select category first</option>';
        businessTypeSelect.setAttribute('disabled', 'disabled');
        return;
    }

    const types = categoriesConfig[category] || [];
    businessTypeSelect.innerHTML = '<option value="">Select type</option>';

    types.forEach(type => {
        const option = document.createElement('option');
        option.value = type;
        option.textContent = type;
        if (type === selectedValue) {
            option.selected = true;
        }
        businessTypeSelect.appendChild(option);
    });

    businessTypeSelect.removeAttribute('disabled');
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

document.querySelectorAll('.form-control').forEach(input => {
    input.addEventListener('input', function() {
        this.classList.remove('is-invalid');
    });
});


// Fix submit button - Force form submission with proper validation
const submitBtn = document.getElementById("btnSubmit");
if (submitBtn) {
    submitBtn.addEventListener("click", function(e) {
        e.preventDefault();
        
        const confirmTrue = document.getElementById("confirm_true");
        const acceptTerms = document.getElementById("accept_terms");
        const form = document.getElementById("b2bRegistrationForm");
        
        if (!confirmTrue || !confirmTrue.checked) {
            alert("Por favor, confirme que as informações são verdadeiras.");
            if (confirmTrue) confirmTrue.focus();
            return false;
        }
        
        if (!acceptTerms || !acceptTerms.checked) {
            alert("Por favor, aceite os termos e condições.");
            if (acceptTerms) acceptTerms.focus();
            return false;
        }
        
        // Use requestSubmit() to trigger HTML5 validation
        if (form.requestSubmit) {
            form.requestSubmit();
        } else {
            // Fallback for older browsers
            form.submit();
        }
    });
}
</script>

@endsection
