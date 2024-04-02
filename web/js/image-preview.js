$(document).ready(function() {
    $(".thumbnail-image").hover(function() {
        $(this).animate({width: "200px", height: "auto"}, 200);
    }, function() {
        $(this).animate({width: "100px", height: "auto"}, 200);
    });

    $(".thumbnail-image").click(function() {
        let imageUrl = $(this).attr("src");
        $("#fullImage").attr("src", imageUrl);
        $("#imageLink").attr("href", imageUrl); // Обновляем ссылку на страницу изображения
        $("#imageModal").modal("show");
        $("#imageModal .modal-dialog").addClass("modal-lg"); // Добавляем класс modal-lg для полустраницы
        $("#imageModal .modal-dialog").addClass("modal-dialog-centered"); // Добавляем класс modal-dialog-centered для центрирования
    });

    // Функция для центрирования изображения внутри модального окна
    function centerImage() {
        let modal = $("#imageModal");
        let modalBody = modal.find(".modal-body");
        let image = modalBody.find("img");
        let imageHeight = image.height();
        let modalBodyHeight = modalBody.height();
        let marginTop = (modalBodyHeight - imageHeight) / 2;
        image.css("margin-top", marginTop + "px");
    }

    // Вызываем функцию центрирования при открытии модального окна
    $("#imageModal").on("shown.bs.modal", function() {
        centerImage();
    });

    // При изменении размеров окна также пересчитываем центрирование изображения
    $(window).resize(function() {
        centerImage();
    });
});
