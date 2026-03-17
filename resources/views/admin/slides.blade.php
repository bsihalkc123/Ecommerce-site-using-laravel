@extends('layouts.admin')

@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">

            <!-- Header -->
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Slides</h3>
                <ul class="breadcrumbs flex items-center flex-wrap gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">Slides</div></li>
                </ul>
            </div>

            <div class="wg-box">

                <!-- Search & Add -->
                <div class="flex items-center justify-between gap10 flex-wrap mb-3">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Search here..." name="name">
                            </fieldset>
                            <div class="button-submit">
                                <button type="submit">
                                    <i class="icon-search"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                    <a class="tf-button style-1 w208"
                       href="{{ route('admin.slide.add') }}">
                        <i class="icon-plus"></i> Add New
                    </a>
                </div>

                <!-- Table -->
                <div class="wg-table table-all-user">
                    @if (session('status'))
                        <p class="alert alert-success text-center">
                        {{ session('status') }}
                        </p>
                    @endif

                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th >#</th>
                                <th >Image</th>
                                <th>Tagline</th>
                                <th>Title</th>
                                <th>Subtitle</th>
                                <th>Link</th>
                                <th>Status</th>
                                <th width="150">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($slides as $slide)
                            <tr>
                                <td>{{ $slide->id }}</td>

                                <!-- Image -->
                                <td>
                                    <img src="{{ asset('uploads/slides/'.$slide->image) }}"
                                         width="60"
                                         height="60"
                                         style="object-fit:cover; border-radius:6px;">
                                </td>

                                <td>{{ $slide->tagline }}</td>
                                <td>{{ $slide->title }}</td>
                                <td>{{ $slide->subtitle }}</td>

                                <!-- Link -->
                                <td style="word-break: break-word;">
                                    <a href="{{ $slide->link }}" target="_blank">
                                        {{ \Illuminate\Support\Str::limit($slide->link, 30) }}
                                    </a>
                                </td>

                                <!-- Status -->
                                <td>
                                    @if($slide->status == 1)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>

                                <!-- Action -->
                                <td>
                                    <div class="list-icon-function d-flex justify-content-center gap-2">

                                        <!-- Edit -->
                                        <a href="{{ route('admin.slide.edit', $slide->id) }}">
                                            <div class="item edit">
                                                <i class="icon-edit-3"></i>
                                            </div>
                                        </a>

                                        <!-- Delete -->
                                        <form action="{{ route('admin.slide.delete', $slide->id) }}"
                                              method="POST">
                                            @csrf
                                            @method('DELETE') 
                                            <div class="item text-danger delete">
                                                <i class="icon-trash-2"></i>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8">No Slides Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $slides->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
@push('scripts')
    <script>
        $(function(){
            $('.delete').on('click', function(e){
                e.preventDefault();
                var form = $(this).closest('form');
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this slide!",
                    type: "warning",
                    buttons: ["No", "Yes"],
                    confirmButtonColor: "#dc3545",                    
                })
                .then (function(result){
                    if (result) {
                        form.submit();
                    }
                });
            });
        });
    </script>    
@endpush