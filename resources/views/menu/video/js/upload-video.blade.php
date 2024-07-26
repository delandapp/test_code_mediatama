<script type="module">
    const $dropAreaVideo = $(".drop-area-video");
    const $fileInputVideo = $("#video");
    const $imageLabelVideo = $("#video-label");
    const $previewContainerVideo = $(".preview-video-container");
    const $previewVideo = $(".preview-video");
    const $closeButtonVideo = $(".close-button-video");
    const $fileNameVideo = $(".file-name-video");
    const allowedTypesVideo = ["video/mp4", "video/webm", "video/ogg"];
    const maxSizeVideo = 20 * 1024 * 1024; // 20 MB

    $dropAreaVideo.on("dragover", (event) => {
        event.preventDefault();
        $dropAreaVideo.addClass("active");
    });

    $dropAreaVideo.on("dragleave", () => {
        $dropAreaVideo.removeClass("active");
    });

    $dropAreaVideo.on("drop", (event) => {
        event.preventDefault();
        const file = event.originalEvent.dataTransfer.files[0];
        showPreviewVideo(file);
        showFileNameVideo(file);
    });

    $fileInputVideo.on("change", () => {
        const file = $fileInputVideo[0].files[0];
        showPreviewVideo(file);
        showFileNameVideo(file);
    });

    $closeButtonVideo.on("click", (event) => {
        event.preventDefault();
        removePreview();
    });

    function removePreview() {
        $fileInputVideo.val("");
        $previewVideo.find("source").attr("src", "");
        $previewVideo[0].load();
        $fileNameVideo.text("");
        $previewVideo.addClass("hidden");
        $previewContainerVideo.addClass("hidden");
        $previewVideo.removeClass("flex");
        $dropAreaVideo
            .removeClass("border-green-500 border-red-500")
            .addClass("border-gray-300");
        $imageLabelVideo.removeClass("hidden").addClass("flex");
    }

    function showPreviewVideo(file) {
        if (!allowedTypesVideo.includes(file.type) || file.size > maxSizeVideo) {
            $(`input[name="video"]`)
                .next(".error-message")
                .text(
                    "File harus berupa berformat MP4, WEBP atau OGG dan ukuran maksimal 20 MB"
                )
                .addClass("flex")
                .removeClass("hidden");
            $(".drop-area-video")
                .removeClass("active border-gray-300 border-green-500")
                .addClass("border-red-500");
            $fileInputVideo.val("");
            $previewVideo.removeClass("flex").addClass("hidden");
            $previewContainerVideo.addClass("hidden");
            $previewVideo.find("source").attr("src", "");
            $imageLabelVideo.addClass("flex").removeClass("hidden");
            return;
        }

        $(`input[name="video"]`)
            .next(".error-message")
            .text("")
            .addClass("hidden");
        $(".drop-area-video")
            .removeClass("border-red-500")
            .addClass("border-green-500");

        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = () => {
            $imageLabelVideo.addClass("hidden").removeClass("flex");
            const videoElement = $previewVideo[0];
            videoElement.src = reader.result;
            videoElement.load();
            video
            $previewVideo.removeClass("hidden");
            $dropAreaVideo.removeClass("active");
            $previewContainerVideo.removeClass("hidden");
            $previewContainerVideo.addClass("flex");
        };
    }

    function showFileNameVideo(file) {
        $fileNameVideo.text(file.name);
        $fileNameVideo.show();
    }
</script>
