<!DOCTYPE html>
<html>
<head>
    <title>Apply Product</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

</head>
<body>

<div class="container mt-4">

    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if(session('status-bad'))
        <div class="alert alert-danger">
            {{ session('status-bad') }}
        </div>
    @endif

    <div class="card">
        <div class="card-header text-center font-weight-bold">
            Apply a product from inventory
        </div>
        <div class="card-body">
            <form name="add-blog-post-form" id="add-blog-post-form" method="post" action="{{url('apply')}}">
                @csrf

                <div class="form-group">
                    <label for="exampleInputEmail1">Quantity to Allocate</label>
                    <input type="number" id="applicationQty" name="applicationQty" class="form-control" required="">
                </div>

                <div class="form-group">
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>

                <div class="alert alert-info">
                    NOTE FROM AARON: To reset the database and stock at hand, run <span class="font-italic font-weight-bold">docker-compose exec app php artisan migrate:fresh --seed</span>
                </div>

            </form>
        </div>
    </div>
</div>
</body>
</html>
