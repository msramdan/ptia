<div class="row mb-2">
    <div class="col-md-6">
        <div class="form-group">
            <textarea name="text_pesan" id="text-pesan" class="form-control @error('text_pesan') is-invalid @enderror"
                placeholder="{{ __('Text Pesan') }}" required>{{ isset($pesanWa) ? $pesanWa->text_pesan : old('text_pesan') }}</textarea>
            @error('text_pesan')
                <span class="text-danger">
                    {{ $message }}
                </span>
            @enderror
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/40.0.0/classic/ckeditor.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        ClassicEditor
            .create(document.querySelector('#text-pesan'), {
                toolbar: [
                    'bold', 'italic', 'link', 'underline', 'strikethrough', '|',
                    'undo', 'redo', '|',
                    'bulletedList', 'numberedList', '|',
                    'blockQuote', '|',
                    'insertTable', 'tableColumn', 'tableRow', 'mergeTableCells'
                ],
            })
            .catch(error => {
                console.error(error);
            });
    });
</script>
