@extends('admin.partials.master')
@section('category_active')
active
@endsection
@section('product_active')
    active
@endsection
@section('title')
     {{ __('Update') }}
@endsection
@section('main-content')
<section class="section">
    <div class="section-body">
        <div class="d-flex justify-content-between">
            <div class="d-block">
                <h2 class="section-title">{{__('Update Category')}}</h2>
            </div>
            @if(hasPermission('category_read'))
                <div class="buttons add-button">
                    <a href="{{ old('r') ? old('r') : (@$r ? $r : url()->previous() )}}" class="btn btn-outline-primary"><i class='bx bx-arrow-back'></i>{{ __('Back') }}</a>
                </div>
            @endif
        </div>
        <div class="row">
            <div class="col-6 middle">
                <div class="card">
                    <div class="card-header input-title">
                        <h4>{{__('Update Category')}}</h4>
                    </div>
                    <div class="card-body card-body-paddding">
                        <form class="" id="lang">
                            <div class="form-group">
                                <label for="name">{{ __('Language') }}</label>
                                <input type="hidden" value="{{ old('r') ? old('r') : (@$r ? $r : url()->previous() )}}" name="r">
                                <select class="form-control selectric lang" name="lang">
                                    <option value="">{{ __('Select Language') }}</option>
                                    @foreach($languages as $language)
                                        <option value="{{ $language->locale }}" {{($lang != '' ? ($language->locale == $lang ? 'selected' : '') : ($language->locale == 'en' ? 'selected' : '')) }}>{{ $language->name }}</option>
                                    @endforeach
                                </select>

                                @if ($errors->has('lang'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('lang') }}</p>
                                    </div>
                                @endif
                            </div>
                        </form>
                        <form method="POST" action="{{route('update.categories')}}" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="form-group">
                                <label for="title">{{ __('Title') }}</label>
                                <input type="hidden" value="{{ $category_language->translation_null == 'not-found' ? '' : $category_language->id }}" name="cat_lang_id">
                                <input type="hidden" value="{{ $category_language->category->id }}" name="category_id">
                                <input type="hidden" value="{{ $lang }}" name="lang">
                                <input type="hidden" value="{{ old('r') ? old('r') : (@$r ? $r : url()->previous() )}}" name="r">
                                <input type="text" name="title" id="title"
                                       value="{{ old('title') ? old('title') : $category_language->title }}"
                                       class="form-control" required>
                                @if ($errors->has('title'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('title') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="name">{{ __('Root Category') }}</label>
                                <select class="form-control select2" name="category">
                                    <option value="">{{ __('Select Root Category') }}</option>
                                    @foreach ($all_categories as $category)
                                        <option value="{{ $category->id }}" {{ $category_language->category->parent_id == $category->id ? 'selected' : ''}}>{{ $category->getTranslation('title', \App::getLocale()) }}</option>
                                        @foreach ($category->childCategories as $childCategory)
                                            @include('admin.products.categories.child-categories', ['child_category' => $childCategory , 'parent' => $category_language->category->parent_id])
                                        @endforeach
                                    @endforeach
                                </select>
                                @if ($errors->has('category'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('category') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="ordering">{{__('Order')}} (<small>{{ __('To show on menu sidebar') }}</small>)</label>
                                <input id="ordering" type="number" class="form-control" name="ordering" value="{{ old('ordering') ? old('ordering') : $category_language->category->ordering }}"
                                       placeholder="{{__('Slug')}}" tabindex="1" >
                                @if ($errors->has('ordering'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('ordering') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="slug">{{__('Slug')}}</label>
                                <input id="slug" type="text" class="form-control" name="slug" value="{{ old('slug') ? old('slug') : $category_language->category->slug }}"
                                       placeholder="{{__('Order')}}" tabindex="1">
                                @if ($errors->has('slug'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('slug') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="commission">{{__('Commission Rate')}}</label>
                                <input id="commission" type="number" class="form-control" step="any" name="commission" value="{{ old('commission') ? old('commission') : $category_language->category->commission }}"
                                       placeholder="{{__('Commission Rate')}} {{__('(%)')}}" tabindex="1">

                                @if ($errors->has('commission'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('commission') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="barcode">{{ __('Icon') }}</label>
                                <div class="input-group category-icon-selector">
                                    <input type="text" name="icon" id="uip-select-input-value"
                                           value="{{ old('icon') ? old('icon') : $category_language->category->icon }}" class="form-control"
                                           placeholder="{{ __('Enter product icon') }}">
                                    <div class="input-group-append" id="uip-select-input">
                                        <div class="input-group-text" id="uip-icon-container">
                                            @if($category_language->category->icon != null || $category_language->category->icon != '' ||  old('icon'))
                                                <i class="{{$category_language->category->icon}}"></i>
                                            @else
                                                Set Icon
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                @if ($errors->has('icon'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('icon') }}</p>
                                    </div>
                                @endif
                            </div>
                            <div class="form-group">
                                <label for="logo">{{ __('Thumbnail') }} ({{ __('72*72') }})</label>
                                <div class="form-group">
                                    <div class="input-group gallery-modal" id="btnSubmit"  data-for="image" data-selection="single"
                                         data-target="#galleryModal" data-dismiss="modal">
                                        <input type="hidden" name="logo" value="{{ old('logo') !='' ? old('logo') : $category_language->category->logo_id }}" class="image-selected">
                                        <span class="form-control"><span class="counter">{{ $category_language->category->logo_id ? 1 : 0 }}</span> {{ __('file chosen') }}</span>
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ __('Choose File') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="selected-media-box">
                                        <div class="mt-4 gallery gallery-md d-flex">
                                            @php
                                                $thumb = old('logo') ? old('logo') : $category_language->category->logo_id;
                                                $thumbnail = \App\Models\Media::find($thumb);
                                            @endphp
                                            @if($thumbnail)
                                                <div class="selected-media mr-2 mb-2 mt-3 ml-0"
                                                     data-id="{{ $thumbnail->id }}">
                                                    @if(is_file_exists($thumbnail->image_variants['image_72x72'], $thumbnail->image_variants['storage']))
                                                        <img
                                                            src="{{ get_media($thumbnail->image_variants['image_72x72'], $thumbnail->image_variants['storage'])}}"
                                                            alt="img-thumbnail"
                                                            class="img-thumbnail logo-profile">
                                                    @else
                                                        <img
                                                            src="{{ static_asset('images/default/default-image-72x72.png') }}"
                                                            alt="img-thumbnail"
                                                            class="img-thumbnail logo-profile">
                                                    @endif
                                                    <div class="image-remove">
                                                        <a href="javascript:void(0)" class="remove"><i
                                                                class="bx bx-x"></i></a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="selected-media mr-2 mb-2 mt-3 ml-0">
                                                    <img src="{{ static_asset('images/default/default-image-72x72.png') }}" alt="{{ $category_language->title }}"
                                                         class="img-thumbnail logo-profile">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="logo">{{ __('Banner') }} ({{ __('835*200') }})</label>
                                <div class="form-group">
                                    <div class="input-group gallery-modal" id="btnSubmit"  data-for="image" data-selection="single"
                                         data-target="#galleryModal" data-dismiss="modal">
                                        <input type="hidden" name="banner" value="{{ old('banner') !='' ? old('banner') : $category_language->category->banner_id }}" class="image-selected">
                                        <span class="form-control"><span class="counter">{{ $category_language->category->banner_id ? 1 : 0 }}</span> {{ __('file chosen') }}</span>
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                {{ __('Choose File') }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="selected-media-box">
                                        <div class="mt-4 gallery gallery-md d-flex">
                                            @php
                                                $banner = old('banner') ? old('banner') : $category_language->category->banner_id;
                                                $thumbnail = \App\Models\Media::find($banner);
                                            @endphp
                                            @if($thumbnail)
                                                <div class="selected-media mr-2 mb-2 mt-3 ml-0"
                                                     data-id="{{ $thumbnail->id }}">
                                                    @if(is_file_exists($thumbnail->image_variants['image_72x72'], $thumbnail->image_variants['storage']))
                                                        <img
                                                            src="{{ get_media($thumbnail->image_variants['image_72x72'], $thumbnail->image_variants['storage'])}}"
                                                            alt="img-thumbnail"
                                                            class="img-thumbnail logo-profile">
                                                    @else
                                                        <img
                                                            src="{{ static_asset('images/default/default-image-72x72.png') }}"
                                                            alt="img-thumbnail"
                                                            class="img-thumbnail logo-profile">
                                                    @endif
                                                    <div class="image-remove">
                                                        <a href="javascript:void(0)" class="remove"><i
                                                                class="bx bx-x"></i></a>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="selected-media mr-2 mb-2 mt-3 ml-0">
                                                    <img src="{{ static_asset('images/default/default-image-72x72.png') }}" alt="{{ $category_language->title }}"
                                                         class="img-thumbnail logo-profile">
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="meta_title">{{__('Meta Title')}}</label>
                                <input id="meta_title" type="text" class="form-control" name="meta_title"
                                       placeholder="{{__('Meta title')}}" tabindex="1" value="{{ old('meta_title') ? old('meta_title') : $category_language->meta_title }}">
                               @if ($errors->has('meta_title'))
                                    <div class="invalid-feedback">
                                        <p>{{ $errors->first('meta_title') }}</p>
                                    </div>
                               @endif
                            </div>
                            <div class="form-group">
                                <label for="meta_description" >{{__('Meta description')}}</label>
                                <textarea class="form-control" name="meta_description" id="meta_description" placeholder="{{__('Description')}}">{{$category_language->meta_description}}</textarea>
                                @if ($errors->has('meta_description'))
                                <div class="invalid-feedback">
                                    <p>{{ $errors->first('meta_description') }}</p>
                                </div>
                               @endif
                            </div>
                            <div class="form-group text-right">
                                <button type="submit" class="btn btn-outline-primary" tabindex="4">
                                    {{ __('Update') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@include('admin.common.selector-modal')
@endsection

@section('style')
    <link rel="stylesheet" href="{{ static_asset('admin/css/dropzone.css') }}">
@endsection
@push('script')
    <script type="text/javascript" src="{{ static_asset('admin/js/dropzone.min.js') }}"></script>
    <script type="text/javascript" src="{{ static_asset('admin/universal-icon-picker/js/universal-icon-picker.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(event) {
            var uip = new UniversalIconPicker('#uip-select-input', {
                iconLibraries: [
                    'materialdesignicons.json'
                ],
                iconLibrariesCss: [
                    '{{ static_asset('frontend/css/materialdesignicons.min.css') }}'
                ],
                resetSelector: '',  // must be an ID or '' if no reset button
                onSelect: function(jsonIconData) {
                    document.getElementById('uip-select-input-value').value = jsonIconData.iconClass;
                    document.getElementById('uip-icon-container').innerHTML = '<i class="'+jsonIconData.iconClass+'"></i>';
                },
                onReset: function() {
                    document.getElementById('uip-select-input-value').value = '';
                }
            });
        });
        document.addEventListener('DOMContentLoaded', function(event) {
            var uip2 = new UniversalIconPicker('#uip-select-input-value', {
                iconLibraries: [
                    'materialdesignicons.json'
                ],
                iconLibrariesCss: [
                    '{{ static_asset('frontend/css/materialdesignicons.min.css') }}'
                ],
                resetSelector: '',  // must be an ID or '' if no reset button
                onSelect: function(jsonIconData) {
                    document.getElementById('uip-select-input-value').value = jsonIconData.iconClass;
                    document.getElementById('uip-icon-container').innerHTML = '<i class="'+jsonIconData.iconClass+'"></i>';
                },
                onReset: function() {
                    document.getElementById('uip-select-input-value').value = '';
                }
            });
        });
    </script>
@endpush
