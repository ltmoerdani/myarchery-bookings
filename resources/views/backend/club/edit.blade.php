<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLongTitle">{{ __('Edit FAQ') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <form id="ajaxEditForm" class="modal-form" action="{{ route('admin.club_management.update_club') }}"
                    method="post">
                    @csrf
                    <input type="hidden" id="in_id" name="id">

                    <div class="form-group">
                        <label class="mb-1">
                            {{ __('Logo Club') }}
                        </label>
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="logo_update_club" name="logo"
                                aria-describedby="logo" accept=".jpg,.jpeg,.png,.svg" style="background:#fff"
                                onchange="handleChooseUpdateLogoClub(this)">
                            <label class="custom-file-label" for="logo" style="background:#fff">
                                {{ __('Choose Image') }}
                            </label>
                        </div>
                        <small>
                            *png, jpg, jpeg, svg only. Max 1mb
                        </small>
                        <div class="mt-2" id="preview_logo_update_club"></div>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Name') . ' *' }}</label>
                        <input type="text" id="in_name" class="form-control ltr" name="name"
                            placeholder="{{ __('Enter Club Name') }}">
                        <p id="editErr_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Place Name') . ' *' }}</label>
                        <textarea style="resize:none" rows="1" id="in_place_name" class="form-control ltr" name="place_name"
                            placeholder="{{ __('Enter Place Name Club') }}"></textarea>
                        <p id="editErr_place_name" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Address') . ' *' }}</label>
                        <textarea style="resize:none" rows="1" id="in_address" class="form-control ltr" name="address"
                            placeholder="{{ __('Enter Address Club') }}"></textarea>
                        <p id="editErr_address" class="mt-1 mb-0 text-danger em"></p>
                    </div>

                    <div class="form-group">
                        <label for="">{{ __('Description') . ' *' }}</label>
                        <textarea style="resize:none" rows="1" id="in_description" class="form-control ltr" name="description"
                            placeholder="{{ __('Enter Descritpion Club') }}"></textarea>
                        <p id="editErr_description" class="mt-1 mb-0 text-danger em"></p>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    {{ __('Close') }}
                </button>
                <button id="updateBtn" type="button" class="btn btn-primary btn-sm">
                    {{ __('Update') }}
                </button>
            </div>
        </div>
    </div>
</div>
<script>
    const handleChooseUpdateLogoClub = (e) => {
        $("#preview_logo_update_club").empty();
        if (!['image/png', 'image/jpg', 'image/jpeg', 'image/svg+xml'].includes(e.files[0].type)) {
            document.getElementById('logo_update_club').value = '';
            return false;
        }
        const blob = new Blob([e.files[0]], {
            type: e.files[0].type
        })
        const blobURL = URL.createObjectURL(blob)
        $("#preview_logo_update_club").append(`
            <img src="${blobURL}" alt="preview" class="rounded customer-img">
        `)
    }
</script>
