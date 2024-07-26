<script type="module">
    const $dropArea = $(".drop-area");
    const $fileInput = $("#image");
    const $imageLabel = $("#image-label");
    const $previewContainer = $(".preview-container");
    const $previewImage = $(".preview-image");
    const $closeButton = $(".close-button");
    const $fileName = $(".file-name");
    const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
    const maxSize = 10 * 1024 * 1024;

    $dropArea.on("dragover", (event) => {
        event.preventDefault();
        $dropArea.addClass("active");
    });

    $dropArea.on("dragleave", () => {
        $dropArea.removeClass("active");
    });

    $dropArea.on("drop", (event) => {
        event.preventDefault();
        const file = event.originalEvent.dataTransfer.files[0];
        showPreview(file);
        showFileName(file);
    });

    $fileInput.on("change", () => {
        const file = $fileInput[0].files[0];
        showPreview(file);
        showFileName(file);
    });

    $closeButton.on("click", (event) => {
        event.preventDefault();
        removePreview();
    });

    function removePreview() {
        $fileInput.val("");
        $previewImage.css("background-image", "");
        $fileName.text("");
        $previewImage.addClass("hidden");
        $previewContainer.addClass("hidden");
        $previewImage.removeClass("flex");
        $dropArea
            .removeClass("border-green-500 border-red-500")
            .addClass("border-gray-300");
        $imageLabel.removeClass("hidden").addClass("flex");
    }

    function showPreview(file) {
        if (!allowedTypes.includes(file.type) || file.size > maxSize) {
            $(`input[name="thumbnail"]`)
                .next(".error-message")
                .text(
                    "File harus berupa berformat JPEG, PNG, atau GIF dan ukuran maksimal 10 MB"
                )
                .addClass("flex")
                .removeClass("hidden");
            $(".drop-area")
                .removeClass("active border-gray-300 border-green-500")
                .addClass("border-red-500");
            $fileInput.val("");
            $previewImage.removeClass("flex").addClass("hidden");
            $previewContainer.addClass("hidden");
            $previewImage.css("background-image", "");
            $imageLabel.addClass("flex").removeClass("hidden");
            return;
        }

        if (file.type.startsWith("image/")) {
            $(`input[name="thumbnail"]`)
                .next(".error-message")
                .text("")
                .addClass("hidden");
            $(".drop-area")
                .removeClass("border-red-500")
                .addClass("border-green-500");
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = () => {
                $imageLabel.addClass("hidden").removeClass("flex");
                $previewImage.css("background-image", `url(${reader.result})`);
                $previewImage.removeClass("hidden");
                $dropArea.removeClass("active");
                $previewContainer.removeClass("hidden");
                $previewContainer.addClass("flex");
            };
        }
    }

    function showFileName(file) {
        $fileName.text(file.name);
        $fileName.show();
    }
</script>
