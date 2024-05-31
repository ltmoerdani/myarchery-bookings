<div class="modal fade" id="createModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Add Club') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxForm" class="modal-form create" action="{{ route('admin.club_management.store_club') }}"
                    method="post">
                    @csrf
                    <div class="form-group">
                        <label class="mb-1">
                            {{ __('Logo Club') }}
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="logo_create_club" name="logo"
                                aria-describedby="logo" accept=".jpg,.jpeg,.png,.svg" style="background:#fff"
                                onchange="handleChooseImage(this)">
                            <label class="custom-file-label" for="logo" style="background:#fff">
                                {{ __('Choose Image') }}
                            </label>
                        </div>
                        <small>
                            *png, jpg, jpeg, svg only. Max 1mb
                        </small>
                        <div class="mt-2" id="preview_logo_create_club"></div>
                    </div>
                    <div class="form-group">
                        <label for="">{{ __('Name') . ' *' }}</label>
                        <input class="form-control" name="name" placeholder="{{ __('Enter Club Name') }}">
                        <p id="err_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Place Name') . ' *' }}</label>
                        <textarea class="form-control ltr" style="resize:none" rows="1" name="place_name"
                            placeholder="{{ __('Enter Place Name Club') }}"></textarea>
                        <p id="err_place_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Address') . '*' }}</label>
                        <textarea class="form-control ltr" style="resize:none" rows="1" name="address"
                            placeholder="{{ __('Enter Address Club') }}"></textarea>
                        <p id="err_address" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Description') }}</label>
                        <textarea class="form-control ltr" style="resize:none" rows="1" name="description"
                            placeholder="{{ __('Enter Descritpion Club') }}"></textarea>
                        <p id="err_description" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    {{ __('Close') }}
                </button>
                <button id="submitBtn" type="button" class="btn btn-primary btn-sm">
                    {{ __('Save') }}
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    const handleChooseImage = (e) => {
        $("#preview_logo_create_club").empty();
        if (!['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'].includes(e.files[0].type)) {
            document.getElementById('logo_create_club').value = '';
            return false;
        }
        const blob = new Blob([e.files[0]], {
            type: e.files[0].type
        })
        const blobURL = URL.createObjectURL(blob)
        $("#preview_logo_create_club").append(`
            <img src="${blobURL}" alt="preview" class="rounded customer-img">
        `)
    }
</script>
