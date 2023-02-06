$(document).ready(function () {
    $('#realtyForm').submit(function (event) {
        event.preventDefault();
        let itemsContainer = $('#itemsContainer');
        let err = $('#err');
        err.html('');
        $.ajax({
            type: 'post',
            url: '/parseRealty',
            datatype: 'json',
            data: $(this).serialize(),
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                if(response.result.success){
                    let coords = [];
                    let offers = response.result.offers;
                    for (let i = 0; i < offers.length; i++) {
                        let offer = offers[i];
                        let latitude = offer['latitude'];
                        let longitude = offer['longitude'];
                        let modalButton = $('<button id="detailsModalBtn" data-offerid="'+offer.offerId+'" type="button" class="btn btn-primary details-modal-button" data-bs-toggle="modal" data-bs-target="#detailsModal">');
                        let itemWrapper = $('<div class="item-wrapper">');
                        let item = $('<div class="item">');
                        let leftSide = $('<div class="left-side">');
                        let rightSide = $('<div class="right-side position-relative">');
                        let descW = $('<div class="descW">');

                        coords[i] = [latitude, longitude];
                        modalButton.html('Details');
                        leftSide.css('background-image', 'url(' + offer.image + ')');
                        rightSide.append($('<h2>', {id: 'itemTitle', class: 'item-title', html: offer.title}));
                        descW.append($('<p>', {id: 'itemDesc', class: 'item-desc', html: offer.description}));
                        descW.append(modalButton);
                        rightSide.append(descW);
                        item.append(leftSide);
                        item.append(rightSide);
                        itemWrapper.append(item);
                        itemsContainer.append(itemWrapper);
                    }
                    /*--maps-- */
                    ymaps.ready(init);

                    function init() {
                        let myMap = new ymaps.Map("map", {
                            center: coords[0],
                            zoom: 10
                        });
                        ymaps.modules.require(['Heatmap'], function (Heatmap) {
                            let data = coords, heatmap = new Heatmap(data);
                            heatmap.setMap(myMap);
                        });
                    }

                    /*--end maps-- */
                }else{
                    err.html(response.result.error);
                    console.log(err)
                }
            },
            error: function (response) {
                let errorPrice = $('#error_price');
                errorPrice.addClass('d-none');
                if (response.status === 422) {
                    var errors = response.responseJSON.errors;
                    $.each(errors, function (key, val) {
                        let errText = val[0];
                        errorPrice.removeClass('d-none').text(errText);
                    });
                }
                if (response.status === 500) {
                    $("#error_500").removeClass('d-none').text('Something went wrong');
                }
            }
        });
    })

    /*---DetailsModal---*/

    $(document).on('click', '#detailsModalBtn', function () {
        let tr = $('#details-row');
        let title = $('.modal-title');
        let date = $('#modalDate');
        let url = $('#modalUrl');
        let offerId = $(this).data('offerid');

        tr.empty();
        title.html('');

        $.ajax({
            url: '/offer/' + offerId,
            type: 'get',
            success: function (response) {
                let data = response.data;
                var dataObj = JSON.parse(data);

                title.html(dataObj.title);
                date.html('Publication date: ' + dataObj.date);
                url.attr("href", dataObj.url);
                tr.append($('<td>', {html: dataObj.address}));
                tr.append($('<td>', {html: dataObj.area + '㎡'}));
                tr.append($('<td>', {html: dataObj.builtYear}));
                tr.append($('<td>', {html: dataObj.price}));
                tr.append($('<td>', {html: dataObj.roomsTotal}));

            }
        });
    });
});
