<form action="{{ url('/s3') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <p>This is test</p>
    <input type="file" name="image">
    <button>Submit</button>

    <img src="{{ $data }}" alt="img">
</form>
