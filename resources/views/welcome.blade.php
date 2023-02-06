<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Realty</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Fonts -->
    <link href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Scripts -->
    <script src="https://api-maps.yandex.ru/2.1/?apikey=452425ee-75ad-4fc5-80de-fac369bd4484&lang=ru_RU"
            type="text/javascript">
        <script src="{{asset('js/yandex.map.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/heatmap.min.js')}}" type="text/javascript"></script>
    <script src="{{asset('js/jquery-3.6.0.js')}}"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    @vite(['resources/js/app.js','resources/sass/app.scss'])

</head>
<body>
<form id="realtyForm" class="realty-form" method="post">
    <div class="form-group">
        <label for="price">Price</label>
        <input type="number" class="form-control" id="price" name="price">
    </div>
    <span id="error_price" class="error text-danger mt-0 d-none"></span>
    <span id="error_500" class="error text-danger mt-0 d-none"></span>
    <button type="submit" class="btn btn-primary form-btn">Search</button>
    <p class="text-danger" id="err"></p>
</form>

<div class="map-container">
    <div id="map" class="map"></div>
</div>
{{--Modal--}}
<div class="modal" id="detailsModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title"></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Address</th>
                        <th>Area</th>
                        <th>Built Year</th>
                        <th>Price</th>
                        <th>Rooms</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr id="details-row"></tr>
                    </tbody>
                </table>
                <p id="modalDate" class="small-info"></p>
                <div class="small-info" >
                    <a id="modalUrl" href="">Go to source page</a>
                </div>
            </div>
        </div>
    </div>
</div>
{{--ItemsContainer--}}
<div class="main-container" id="mainContainer">
    <div class="items-container" id="itemsContainer"></div>
</div>
<script src="{{asset('js/script.js')}}" type="text/javascript"></script>
</body>
</html>
