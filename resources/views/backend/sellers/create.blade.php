@extends('backend.layouts.app')

@section('content')
@if (env('MAIL_USERNAME') == null && env('MAIL_PASSWORD') == null)
    <div class="alert alert-info d-flex align-items-center">
        {{ translate('You need to configure SMTP correctly to add Seller.') }}
        <a class="alert-link ml-2" href="{{ route('smtp_settings.index') }}">{{ translate('Configure Now') }}</a>
    </div>
@endif

<div class="aiz-titlebar text-left mt-2 mb-3">
    <div class="row align-items-center">
        <div class="col-md-6">
            <h5 class="mb-0 h6">{{translate('Add New Seller')}}</h5>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-10 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0 h6">{{translate('Seller Registration Form')}}</h5>
                <p class="text-muted mb-0 small">{{translate('All fields marked with * are required')}}</p>
            </div>
            <div class="card-body">
                <form action="{{ route('sellers.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <!-- SECTION 1: Personal Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{translate('1. Personal Information')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name">{{translate('Full Name')}} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('name') is-invalid @enderror"
                                               name="name"
                                               value="{{ old('name') }}"
                                               placeholder="{{translate('Enter full name')}}"
                                               required>
                                        @error('name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email">{{translate('Email Address')}} <span class="text-danger">*</span></label>
                                        <input type="email"
                                               class="form-control @error('email') is-invalid @enderror"
                                               name="email"
                                               value="{{ old('email') }}"
                                               placeholder="{{translate('email@example.com')}}"
                                               required>
                                        @error('email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone">{{translate('Phone Number')}} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('phone') is-invalid @enderror"
                                               name="phone"
                                               value="{{ old('phone') }}"
                                               placeholder="{{translate('+244 900 000 000')}}"
                                               required>
                                        @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">{{translate('Contact number for seller')}}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: Shop/Business Information -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{translate('2. Shop/Business Information')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="shop_name">{{translate('Shop Name')}} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('shop_name') is-invalid @enderror"
                                               name="shop_name"
                                               value="{{ old('shop_name') }}"
                                               placeholder="{{translate('Enter shop name')}}"
                                               required>
                                        @error('shop_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_id">{{translate('NIF / Tax ID')}} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('tax_id') is-invalid @enderror"
                                               name="tax_id"
                                               value="{{ old('tax_id') }}"
                                               placeholder="{{translate('Enter NIF')}}"
                                               required>
                                        @error('tax_id')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">{{translate('Número de Identificação Fiscal')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="registration_number">{{translate('Commercial Registration Number')}}</label>
                                        <input type="text"
                                               class="form-control @error('registration_number') is-invalid @enderror"
                                               name="registration_number"
                                               value="{{ old('registration_number') }}"
                                               placeholder="{{translate('Enter registration number')}}">
                                        @error('registration_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_type">{{translate('Company Type')}}</label>
                                        <select class="form-control aiz-selectpicker @error('company_type') is-invalid @enderror"
                                                name="company_type">
                                            <option value="">{{translate('Select company type')}}</option>
                                            <option value="sole_proprietor" {{ old('company_type') == 'sole_proprietor' ? 'selected' : '' }}>{{translate('Sole Proprietor / Individual')}}</option>
                                            <option value="lda" {{ old('company_type') == 'lda' ? 'selected' : '' }}>{{translate('Sociedade por Quotas (Lda)')}}</option>
                                            <option value="sa" {{ old('company_type') == 'sa' ? 'selected' : '' }}>{{translate('Sociedade Anónima (SA)')}}</option>
                                            <option value="cooperative" {{ old('company_type') == 'cooperative' ? 'selected' : '' }}>{{translate('Cooperative')}}</option>
                                            <option value="other" {{ old('company_type') == 'other' ? 'selected' : '' }}>{{translate('Other')}}</option>
                                        </select>
                                        @error('company_type')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="industry">{{translate('Industry / Sector')}}</label>
                                        <input type="text"
                                               class="form-control @error('industry') is-invalid @enderror"
                                               name="industry"
                                               value="{{ old('industry') }}"
                                               placeholder="{{translate('e.g., Electronics, Fashion, Food')}}">
                                        @error('industry')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 3: Address & Contact -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{translate('3. Address & Contact Information')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">{{translate('Physical Address')}} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('address') is-invalid @enderror"
                                               name="address"
                                               value="{{ old('address') }}"
                                               placeholder="{{translate('Street address, building, suite')}}"
                                               required>
                                        @error('address')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="city">{{translate('City')}} <span class="text-danger">*</span></label>
                                        <input type="text"
                                               class="form-control @error('city') is-invalid @enderror"
                                               name="city"
                                               value="{{ old('city') }}"
                                               placeholder="{{translate('City name')}}"
                                               required>
                                        @error('city')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="province">{{translate('Province')}}</label>
                                        <select class="form-control aiz-selectpicker @error('province') is-invalid @enderror"
                                                name="province">
                                            <option value="">{{translate('Select province')}}</option>
                                            <option value="Luanda" {{ old('province') == 'Luanda' ? 'selected' : '' }}>Luanda</option>
                                            <option value="Benguela" {{ old('province') == 'Benguela' ? 'selected' : '' }}>Benguela</option>
                                            <option value="Huíla" {{ old('province') == 'Huíla' ? 'selected' : '' }}>Huíla</option>
                                            <option value="Huambo" {{ old('province') == 'Huambo' ? 'selected' : '' }}>Huambo</option>
                                            <option value="Cabinda" {{ old('province') == 'Cabinda' ? 'selected' : '' }}>Cabinda</option>
                                            <option value="Kwanza Sul" {{ old('province') == 'Kwanza Sul' ? 'selected' : '' }}>Kwanza Sul</option>
                                            <option value="Kwanza Norte" {{ old('province') == 'Kwanza Norte' ? 'selected' : '' }}>Kwanza Norte</option>
                                            <option value="Bié" {{ old('province') == 'Bié' ? 'selected' : '' }}>Bié</option>
                                            <option value="Other" {{ old('province') == 'Other' ? 'selected' : '' }}>{{translate('Other')}}</option>
                                        </select>
                                        @error('province')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="postal_code">{{translate('Postal Code')}}</label>
                                        <input type="text"
                                               class="form-control @error('postal_code') is-invalid @enderror"
                                               name="postal_code"
                                               value="{{ old('postal_code') }}"
                                               placeholder="{{translate('Postal code')}}">
                                        @error('postal_code')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="company_email">{{translate('Company Email')}}</label>
                                        <input type="email"
                                               class="form-control @error('company_email') is-invalid @enderror"
                                               name="company_email"
                                               value="{{ old('company_email') }}"
                                               placeholder="{{translate('company@example.com')}}">
                                        @error('company_email')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                        <small class="form-text text-muted">{{translate('Public business email')}}</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="website">{{translate('Website')}}</label>
                                        <input type="url"
                                               class="form-control @error('website') is-invalid @enderror"
                                               name="website"
                                               value="{{ old('website') }}"
                                               placeholder="{{translate('https://www.example.com')}}">
                                        @error('website')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 4: Documents (KYC) -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{translate('4. Business Documents (KYC)')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <i class="las la-info-circle"></i> {{translate('Please upload clear copies of your business documents. Accepted formats: PDF, JPG, PNG. Maximum size: 5MB per file.')}}
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="business_license">{{translate('Business License / Certidão Comercial')}} <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file"
                                                   class="custom-file-input @error('business_license') is-invalid @enderror"
                                                   name="business_license"
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                   required>
                                            <label class="custom-file-label" for="business_license">{{translate('Choose file')}}</label>
                                        </div>
                                        @error('business_license')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="tax_certificate">{{translate('Tax Certificate / NIF Document')}} <span class="text-danger">*</span></label>
                                        <div class="custom-file">
                                            <input type="file"
                                                   class="custom-file-input @error('tax_certificate') is-invalid @enderror"
                                                   name="tax_certificate"
                                                   accept=".pdf,.jpg,.jpeg,.png"
                                                   required>
                                            <label class="custom-file-label" for="tax_certificate">{{translate('Choose file')}}</label>
                                        </div>
                                        @error('tax_certificate')
                                            <span class="text-danger small">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 5: Bank Details (for seller payouts) -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{translate('5. Bank Account Details')}}</h6>
                            <small class="text-muted">{{translate('For receiving payouts from sales')}}</small>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_name">{{translate('Bank Name')}}</label>
                                        <input type="text"
                                               class="form-control @error('bank_name') is-invalid @enderror"
                                               name="bank_name"
                                               value="{{ old('bank_name') }}"
                                               placeholder="{{translate('e.g., BAI, BFA, BIC')}}">
                                        @error('bank_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_number">{{translate('Account Number / NIB')}}</label>
                                        <input type="text"
                                               class="form-control @error('bank_account_number') is-invalid @enderror"
                                               name="bank_account_number"
                                               value="{{ old('bank_account_number') }}"
                                               placeholder="{{translate('Account number')}}">
                                        @error('bank_account_number')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="bank_account_name">{{translate('Account Holder Name')}}</label>
                                        <input type="text"
                                               class="form-control @error('bank_account_name') is-invalid @enderror"
                                               name="bank_account_name"
                                               value="{{ old('bank_account_name') }}"
                                               placeholder="{{translate('Name on bank account')}}">
                                        @error('bank_account_name')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="iban">{{translate('IBAN (if applicable)')}}</label>
                                        <input type="text"
                                               class="form-control @error('iban') is-invalid @enderror"
                                               name="iban"
                                               value="{{ old('iban') }}"
                                               placeholder="{{translate('IBAN')}}">
                                        @error('iban')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 6: Additional Notes -->
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <h6 class="mb-0">{{translate('6. Additional Information')}}</h6>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label for="admin_notes">{{translate('Admin Notes')}}</label>
                                <textarea class="form-control @error('admin_notes') is-invalid @enderror"
                                          name="admin_notes"
                                          rows="3"
                                          placeholder="{{translate('Internal notes about this seller (not visible to seller)')}}">{{ old('admin_notes') }}</textarea>
                                @error('admin_notes')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="form-group mb-0 text-right">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="las la-save"></i> {{translate('Create Seller Account')}}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script>
    // Update custom file input label with selected filename
    $('.custom-file-input').on('change', function() {
        var fileName = $(this).val().split('\\').pop();
        $(this).siblings('.custom-file-label').addClass('selected').html(fileName);
    });
</script>
@endsection
