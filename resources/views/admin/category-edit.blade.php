@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>Brand Information</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.categories') }}">
                        <div class="text-tiny">Categories</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li><div class="text-tiny">Edit Category</div></li>
            </ul>
        </div>

        <div class="wg-box">
            <form class="form-new-product form-style-1"
                  action="{{ route('admin.category.update') }}"
                  method="POST"
                  enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <input type="hidden" name="id" value="{{ $category->id }}">

                {{-- Category Name --}}
                <fieldset class="name">
                    <div class="body-title">
                        Category Name <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow"
                           type="text"
                           name="name"
                           value="{{ old('name', $category->name) }}"
                           required>
                </fieldset>
                @error('name')
                    <span class="alert alert-danger">{{ $message }}</span>
                @enderror

                {{-- Category Slug --}}
                <fieldset class="name">
                    <div class="body-title">
                        Category Slug <span class="tf-color-1">*</span>
                    </div>
                    <input class="flex-grow"
                           type="text"
                           name="slug"
                           value="{{ old('slug', $category->slug) }}"
                           required>
                </fieldset>
                @error('slug')
                    <span class="alert alert-danger">{{ $message }}</span>
                @enderror

                {{-- Category Image (Optional) --}}
                <fieldset>
                    <div class="body-title">
                        Category Image
                        <small class="text-muted"></small>
                    </div>

                    <div class="upload-image flex-grow">
                        <div class="item" id="imgpreview">
                            <img src="{{ asset('uploads/categories/' . $category->image) }}?v={{ time() }}"
                                class="effect8"
                                alt="Category Image">
                        </div>

                        <div class="item up-load">
                            <label class="uploadfile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">
                                    Drop image here or
                                    <span class="tf-color">click to browse</span>
                                </span>
                                <input type="file"
                                       id="myFile"
                                       name="image"
                                       accept="image/*">
                            </label>
                        </div>
                    </div>
                </fieldset>

                @error('image')
                    <span class="alert alert-danger">{{ $message }}</span>
                @enderror

                <div class="bot">
                    <button class="tf-button w208" type="submit">
                        Save
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
