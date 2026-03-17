@extends('layouts.admin')

@section('content')
<div class="main-content">
    <div class="main-content-inner">

        <div class="main-content-wrap">

            <!-- Page Header -->
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Slide</h3>

                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <a href="{{ route('admin.slides') }}">
                            <div class="text-tiny">Slides</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li>
                        <div class="text-tiny">New Slide</div>
                    </li>
                </ul>
            </div>
            <!-- /Page Header -->


            <div class="wg-box">

                {{-- Success Message --}}
                @if(session('status'))
                    <div style="color:green; margin-bottom:15px;">
                        {{ session('status') }}
                    </div>
                @endif

                {{-- Validation Errors --}}
                @if($errors->any())
                    <div style="color:red; margin-bottom:15px;">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif


                <form class="form-new-product form-style-1"
                      action="{{ route('admin.slide.store') }}"
                      method="POST"
                      enctype="multipart/form-data">
                    @csrf

                    <!-- Tagline -->
                    <fieldset class="name">
                        <div class="body-title">
                            Tagline <span class="tf-color-1">*</span>
                        </div>
                        <input class="flex-grow"
                               type="text"
                               name="tagline"
                               placeholder="Tagline"
                               value="{{ old('tagline') }}"
                               required>
                    </fieldset>
                @error('tagline')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                    <!-- Title -->
                    <fieldset class="name">
                        <div class="body-title">
                            Title <span class="tf-color-1">*</span>
                        </div>
                        <input class="flex-grow"
                               type="text"
                               name="title"
                               placeholder="Title"
                               value="{{ old('title') }}"
                               required>
                    </fieldset>
                @error('title')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                    <!-- Subtitle -->
                    <fieldset class="name">
                        <div class="body-title">
                            Subtitle <span class="tf-color-1">*</span>
                        </div>
                        <input class="flex-grow"
                               type="text"
                               name="subtitle"
                               placeholder="Subtitle"
                               value="{{ old('subtitle') }}"
                               required>
                    </fieldset>
                @error('subtitle')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                    <!-- Link -->
                    <fieldset class="name">
                        <div class="body-title">
                            Link <span class="tf-color-1">*</span>
                        </div>
                        <input class="flex-grow"
                               type="text"
                               name="link"
                               placeholder="Link"
                               value="{{ old('link') }}"
                               required>
                    </fieldset>
                @error('link')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                    <!-- Image Upload -->
                    <fieldset>
                        <div class="body-title">
                            Upload Image <span class="tf-color-1">*</span>
                        </div>

                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" style="display:none;">
                                <img src="sample.jpg" class="effect8" alt="/">
                            </div>
                            <div class="item" id="imgpreview" style="display:none;">
                                <img src="#" class="effect8" alt="Preview">
                            </div>

                            <label class="uploadfile" for="myFile">
                                <span class="icon">
                                    <i class="icon-upload-cloud"></i>
                                </span>
                                <span class="body-text">
                                    Drop your image here or select
                                    <span class="tf-color">click to browse</span>
                                </span>

                                <input type="file"
                                       id="myFile"
                                       name="image"
                                       accept="image/*"
                                       required>
                            </label>
                        </div>
                    </fieldset>
                @error('image')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                    <!-- Status -->
                    <fieldset class="category">
                        <div class="body-title">Status</div>
                        <div class="select flex-grow">
                            <select name="status" required>
                                <option value="">Select</option>
                                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>
                                    Inactive
                                </option>
                            </select>
                        </div>
                    </fieldset>
                @error('status')<span class="alert alert-danger text-center">{{ $message }}</span>@enderror

                    <!-- Submit -->
                    <div class="bot">
                        <div></div>
                        <button class="tf-button w208" type="submit">
                            Save
                        </button>
                    </div>

                </form>
            </div>

        </div>
    </div>
</div>
@endsection


@push('scripts')
<script>
    $(function(){
        $('#myFile').on("change", function(){
            const [file] = this.files;
            if(file){
                $('#imgpreview img').attr('src', URL.createObjectURL(file));
                $('#imgpreview').show();
            }
        });
    });
</script>
@endpush