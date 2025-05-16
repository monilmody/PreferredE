<?php
// Include the database connection file
include("./header.php");
include("./session_page.php");
include_once("config.php");

// Initialize column preferences if not set
if (!isset($_SESSION['column_prefs'])) {
    $_SESSION['column_prefs'] = [
        'Horse' => true,
        'Yearfoal' => true,
        'Sex' => true,
        'Sire' => true,
        'Dam' => true
    ];
}

// Handle column preference updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['column_prefs'])) {
    // Convert string "true"/"false" to boolean values
    $prefs = [];
    foreach ($_POST['column_prefs'] as $col => $value) {
        $prefs[$col] = ($value === 'true');
    }
    $_SESSION['column_prefs'] = $prefs;
    header("Location: horse_list.php");
    exit();
}

// Get sort parameters
$sort1_param = $_GET['sort1'] ?? '';
$sort2_param = $_GET['sort2'] ?? '';
$sort3_param = $_GET['sort3'] ?? '';
$sort4_param = $_GET['sort4'] ?? '';
$sort5_param = $_GET['sort5'] ?? '';

// Fetch horse data using your existing function
$result = fetchHorseList($sort1_param, $sort2_param, $sort3_param, $sort4_param, $sort5_param);

// Define sortable columns for the dropdowns
$sortList = array("Horse", "Yearfoal", "Sex", "Sire", "Dam");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="assets/css/table.css">
    <link rel="stylesheet" href="assets/css/horse-list.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.css">
    <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="assets/js/script.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
</head>

<br>

<h1 style="text-align:center;color:#FF6B35;">HORSE LIST - STANDARDBRED</h1>

<!-- Sorting Dropdowns -->
<br>
<select style="background-color:#229954;" class="custom-select1" id="sort1">
    <option value="">Sort By 1st</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . $row . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort2">
    <option value="">Sort By 2nd</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . $row . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort3">
    <option value="">Sort By 3rd</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . $row . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort4">
    <option value="">Sort By 4th</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . $row . '">' . $row . '</option>';
    } ?>
</select>

<select style="background-color:#229954;" class="custom-select1" id="sort5">
    <option value="">Sort By 5th</option>
    <?php foreach ($sortList as $row) {
        echo '<option value="' . $row . '">' . $row . '</option>';
    } ?>
</select>

<input class="custom-select1" type="submit" onclick="getValues()" name="SUBMITBUTTON" value="Submit" style="font-size:20px;" />
<br>

<body>
    <!-- Button Container -->
    <div class="button-container">
        <!-- Column Selector Button -->
        <button id="columnSelectorBtn">Select Columns</button>
    </div>

    <!-- Column Checkboxes (will be moved to modal by JavaScript) -->
    <div id="columnCheckboxes" style="display:none;">
        <?php foreach ($_SESSION['column_prefs'] as $col => $visible): ?>
            <div class="column-checkbox">
                <label>
                    <input type="checkbox" name="column_prefs[<?php echo $col; ?>]"
                        <?php echo $visible ? 'checked' : ''; ?>>
                    <?php echo $col; ?>
                </label>
            </div>
        <?php endforeach; ?>
    </div>


    <!-- Table Section -->
    <div class="responsive-table-container">
        <table>
            <thead>
                <tr>
                    <?php foreach ($_SESSION['column_prefs'] as $col => $visible): ?>
                        <?php if ($visible): ?>
                            <th class="col-<?php echo strtolower($col); ?>"><?php echo $col; ?></th>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                    <tr>
                        <?php foreach ($_SESSION['column_prefs'] as $col => $visible): ?>
                            <?php if ($visible): ?>
                                <td class="col-<?php echo strtolower($col); ?>">
                                    <?php if ($col === 'Horse'): ?>
                                        <a href="#" class="horse-link" data-horse-id="<?php echo htmlspecialchars($row['horse']); ?>">
                                            <?php echo htmlspecialchars($row['horse']); ?>
                                        </a>
                                    <?php else: ?>
                                        <?php
                                        $dbField = '';
                                        switch ($col) {
                                            case 'Yearfoal':
                                                $dbField = 'YEARFOAL';
                                                break;
                                            case 'Sex':
                                                $dbField = 'sex';
                                                break;
                                            case 'Sire':
                                                $dbField = 'sire';
                                                break;
                                            case 'Dam':
                                                $dbField = 'dam';
                                                break;
                                            default:
                                                $dbField = strtolower($col);
                                        }
                                        echo htmlspecialchars($row[$dbField] ?? '');
                                        ?>
                                    <?php endif; ?>
                                </td>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Right-side Sidebar for Horse Details -->
    <div id="horseDetailsSidebar" class="sidebar">
        <h2 id="horseName"></h2>

        <!-- Tab buttons -->
        <div class="tab-buttons">
            <button class="tab-button active" data-tab="detailsTab">Details</button>
            <button class="tab-button" data-tab="photosTab">Photos</button>
        </div>

        <!-- Tab content -->
        <div class="tab-content">
            <div id="detailsTab" class="tab-pane active">
                <div id="horseDetailsContent">
                    <p><strong>Year of Foal:</strong> <span id="yearFoalDisplay"></span> <input type="text" id="yearFoalInput" style="display:none;"></p>
                    <p><strong>Sex:</strong> <span id="sexDisplay"></span> <input type="text" id="sexInput" style="display:none;"></p>
                    <p><strong>Sire:</strong> <span id="sireDisplay"></span> <input type="text" id="sireInput" style="display:none;"></p>
                    <p><strong>Dam:</strong> <span id="damDisplay"></span> <input type="text" id="damInput" style="display:none;"></p>
                </div>
                <button id="editBtn" class="btn btn-primary">Edit</button>
                <button id="saveBtn" class="btn btn-success" style="display:none;">Save</button>
                <button id="cancelBtn" class="btn btn-danger" style="display:none;">Cancel</button>
            </div>
            <div id="photosTab" class="tab-pane">
                <div id="photoPreview"></div>
                <!-- 📸 Photo Upload Section -->
                <div id="photoSection" style="margin-top: 20px;">
                    <h3>Photos</h3>
                    <form id="fileUploadForm" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="horseId" id="hiddenHorseId">
                        <input type="file" name="file" id="fileInput" accept="image/*">
                        <button type="submit" class="btn btn-success" style="display:none;">
                            <i class="fas fa-upload"></i> Upload File
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <button class="closebtn" onclick="closeSidebar()">X</button>
    </div>
    <br>


    <script>
        // Function to collect selected sort values and pass them as parameters
        function getValues() {
            var sort1 = document.getElementById('sort1').value;
            var sort2 = document.getElementById('sort2').value;
            var sort3 = document.getElementById('sort3').value;
            var sort4 = document.getElementById('sort4').value;
            var sort5 = document.getElementById('sort5').value;

            var link = "horse_list.php?sort1=" + sort1 +
                "&sort2=" + sort2 +
                "&sort3=" + sort3 +
                "&sort4=" + sort4 +
                "&sort5=" + sort5;

            window.location.href = link;
        }

        // File Upload Handler
        $('#fileUploadForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const fileInput = $('#fileInput')[0];

            if (fileInput.files.length === 0) {
                alert('Please select a file to upload');
                return;
            }

            uploadFileToServer(formData);
        });

        function uploadFileToServer(formData) {
            $.ajax({
                url: 'upload_photo.php',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(response) {
                    console.log('Raw response:', response); // Debugging line
                    if (response && response.success) {
                        addFileToGallery(response);
                        alert('File uploaded successfully!');
                    } else {
                        alert('Upload failed: ' + (response?.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Upload error:', xhr.responseText);
                    try {
                        const errResponse = JSON.parse(xhr.responseText);
                        alert('Upload failed: ' + (errResponse.error || 'Unknown error'));
                    } catch (e) {
                        alert('Upload failed. Server response: ' + xhr.responseText);
                    }
                }
            });
        }

        $(document).ready(function() {
            // Handle file selection
            $('#fileInput').on('change', function(event) {
                const file = event.target.files[0];

                if (!file) {
                    return;
                }

                // Show preview of the image
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = `
                <div class="photo-container">
                    <img src="${e.target.result}" class="uploaded-photo" />
                </div>`;
                    $('#photoPreview').html(img);

                    // Automatically upload the file
                    uploadFile(file);
                };
                reader.readAsDataURL(file); // Preview the image
            });

            // Function to upload the file automatically when selected
            function uploadFile(file) {
                const formData = new FormData();
                const horseId = $('#hiddenHorseId').val(); // Get horseId

                formData.append('file', file);
                formData.append('horseId', horseId); // Add horseId to the FormData

                $.ajax({
                    url: 'upload_photo.php', // The server-side script to handle file upload
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        console.log('Upload response:', response);
                        if (response && response.success) {
                            addFileToGallery(response);
                            alert('File uploaded successfully!');

                            // Trigger page reload and open the sidebar photo section
                            window.location.reload(); // Reload the page

                        } else {
                            alert('Upload failed: ' + (response?.error || 'Unknown error'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Upload error:', xhr.responseText);
                        try {
                            const errResponse = JSON.parse(xhr.responseText);
                            alert('Upload failed: ' + (errResponse.error || 'Unknown error'));
                        } catch (e) {
                            alert('Upload failed. Server response: ' + xhr.responseText);
                        }
                    }
                });
            }

            // Adds the uploaded file to the gallery after successful upload
            function addFileToGallery(fileInfo) {
                const gallery = $('#photoPreview');

                // Remove "no files" message if present
                gallery.find('.no-files-message').remove();

                const isImage = fileInfo.name.match(/\.(jpg|jpeg|png|gif|webp)$/i);
                const fileElement = `
            <div class="file-item" data-id="${fileInfo.id}">
                ${isImage ? 
                    `<img src="${fileInfo.url}" class="file-thumbnail">` : 
                    `<div class="file-icon">
                        <i class="fas fa-file"></i>
                        <span>${fileInfo.name.split('.').pop()}</span>
                    </div>`}
                <div class="file-details">
                    <a href="${fileInfo.url}" target="_blank" class="file-link">
                        ${fileInfo.name}
                    </a>
                    <button class="delete-file" data-id="${fileInfo.id}">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>`;

                gallery.append(fileElement);
            }
        });

        // Check if page was just reloaded after upload
        $(document).ready(function() {
            // Open the sidebar and show the photo section after reload
            if (window.location.hash === "#photosTab") {
                $('#horseDetailsSidebar').addClass('open');
                $('#photoSection').show(); // Show the photo section of the sidebar
            }
        });

        // Function to refresh the page and open the sidebar photo section
        function openSidebarWithPhotos() {
            // Set the hash to trigger the sidebar to open
            window.location.hash = "photosTab";
            $('#horseDetailsSidebar').addClass('open');
            $('#photoSection').show(); // Make sure the photo section is visible
        }

        // Optional: Stop camera when sidebar closes
        function closeSidebar() {
            $('#horseDetailsSidebar').removeClass('open');
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop());
            }
        }

        function sanitizeHorseId(name) {
            return name.replace(/[^a-zA-Z0-9_-]/g, '');
        }

        // Open Sidebar and fetch horse details
        function openSidebar(horseName) {
            console.log("Horse ID (sanitized): " + horseName); // Debugging log

            $.ajax({
                url: 'get_horse_details.php',
                type: 'GET',
                data: {
                    horseId: horseName
                },
                dataType: 'json',
                success: function(response) {
                    console.log("Response:", response); // Debugging log

                    if (response.error) {
                        alert("Error: " + response.error);
                    } else {
                        // Set horse details
                        $('#horseName').text(response.HORSE);

                        $('#horseDetailsContent').html(`
    <p><strong>Year of Foal:</strong> 
        <span id="yearFoalDisplay">${response.YEARFOAL}</span>
        <input type="text" id="yearFoalInput" value="${response.YEARFOAL}" style="display:none;">
    </p>
    <p><strong>Sex:</strong> 
        <span id="sexDisplay">${response.SEX}</span>
        <input type="text" id="sexInput" value="${response.SEX}" style="display:none;">
    </p>
    <p><strong>Sire:</strong> 
        <span id="sireDisplay">${response.Sire}</span>
        <input type="text" id="sireInput" value="${response.Sire}" style="display:none;">
    </p>
    <p><strong>Dam:</strong> 
        <span id="damDisplay">${response.DAM}</span>
        <input type="text" id="damInput" value="${response.DAM}" style="display:none;">
    </p>
`);

                        // Handle images (display the uploaded images)
                        let imagesHtml = '';
                        if (response.images && response.images.length > 0) {
                            response.images.forEach(imgUrl => {
                                imagesHtml += `
<div class="photo-card">
    <img src="${imgUrl}" class="photo-thumbnail" data-full-url="${imgUrl}" />
    <button class="delete-photo" data-url="${imgUrl}">×</button>
</div>`;
                            });
                        } else {
                            imagesHtml = '<p>No photos uploaded yet for this horse.</p>';
                        }

                        // Display images in the sidebar
                        $('#photoPreview').html(imagesHtml);

                        // Show the sidebar
                        const horseIdForImages = sanitizeHorseId(response.HORSE);
                        $('#hiddenHorseId').val(horseIdForImages); // Assuming you have a hidden input for horseId
                        $('#horseDetailsSidebar').addClass('open');
                        $('#photoSection').show();

                        // Show Edit, Save and Cancel buttons
                        $('#editBtn').show();
                        $('#saveBtn').hide();
                        $('#cancelBtn').hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    alert("Failed to load horse details.");
                }
            });
        }

        $(document).on('click', '.tab-button', function() {
            $('.tab-button').removeClass('active');
            $(this).addClass('active');

            const tabToShow = $(this).data('tab');
            $('.tab-pane').removeClass('active');
            $('#' + tabToShow).addClass('active');
        });

        $(document).on('click', '.photo-thumbnail', function() {
            const fullImageUrl = $(this).data('full-url');

            // Remove any existing modal if it exists
            $('#imageModal').remove();

            // Create modal HTML structure dynamically
            const modalHtml = `
        <div id="imageModal" class="modal" style="display: none;">
            <span class="modal-close">&times;</span>
            <img class="modal-content" id="fullImage" src="${fullImageUrl}">
        </div>
    `;

            // Append modal to body
            $('body').append(modalHtml);

            // Show modal
            $('#imageModal').fadeIn();
            $('body').addClass('modal-open');
        });

        $(document).on('click', '.delete-photo', function() {
            const imageUrl = $(this).data('url');
            const parentElement = $(this).closest('.photo-card');

            if (confirm("Are you sure you want to delete this image?")) {
                $.ajax({
                    url: 'delete_photo.php',
                    type: 'POST',
                    data: {
                        imageUrl: imageUrl
                    },
                    success: function(response) {
                        try {
                            const res = typeof response === 'string' ? JSON.parse(response) : response;
                            if (res.success) {
                                parentElement.remove(); // Remove image from UI
                                alert('Image deleted successfully.');
                            } else {
                                alert('Failed to delete image: ' + (res.error || 'Unknown error'));
                            }
                        } catch (e) {
                            console.error('Invalid response:', response);
                            alert('Unexpected server response.');
                        }
                    },
                    error: function(xhr) {
                        console.error('Delete error:', xhr.responseText);
                        alert('Failed to delete image.');
                    }
                });
            }
        });

        // Close when clicking close button
        $(document).on('click', '.modal-close', function() {
            $('#imageModal').fadeOut(function() {
                $(this).remove();
            });
            $('body').removeClass('modal-open');
        });

        // Close when clicking outside the image
        $(document).on('click', '#imageModal', function(e) {
            if (e.target === this) {
                $(this).fadeOut(function() {
                    $(this).remove();
                });
                $('body').removeClass('modal-open');
            }
        });

        // Horse link click
        $(document).on('click', '.horse-link', function(event) {
            event.preventDefault();
            const horseId = $(this).data('horse-id');
            openSidebar(horseId);
        });

        // Close camera functionality
        $('#closeCameraBtn').on('click', function() {
            if (videoStream) {
                videoStream.getTracks().forEach(track => track.stop()); // Stop all camera tracks
            }
            $('#cameraContainer').hide(); // Hide the camera container
        });

        // Edit button: Switch to edit mode
        $(document).on('click', '#editBtn', function() {
            // Show input fields and hide display text
            $('#yearFoalDisplay').hide();
            $('#sexDisplay').hide();
            $('#sireDisplay').hide();
            $('#damDisplay').hide();
            $('#yearFoalInput').show();
            $('#sexInput').show();
            $('#sireInput').show();
            $('#damInput').show();

            // Show Save and Cancel buttons
            $('#editBtn').hide();
            $('#saveBtn').show();
            $('#cancelBtn').show();
        });

        // Cancel button: Revert back to view mode
        $(document).on('click', '#cancelBtn', function() {
            // Revert back to display text and hide input fields
            $('#yearFoalDisplay').show();
            $('#sexDisplay').show();
            $('#sireDisplay').show();
            $('#damDisplay').show();
            $('#yearFoalInput').hide();
            $('#sexInput').hide();
            $('#sireInput').hide();
            $('#damInput').hide();

            // Show Edit button and hide Save/Cancel buttons
            $('#editBtn').show();
            $('#saveBtn').hide();
            $('#cancelBtn').hide();
        });

        // Save button: Send the updated data to the server
        $(document).on('click', '#saveBtn', function() {
            // Get updated values from input fields
            var updatedYearFoal = $('#yearFoalInput').val();
            var updatedSex = $('#sexInput').val();
            var updatedSire = $('#sireInput').val();
            var updatedDam = $('#damInput').val();

            var horseId = $('#hiddenHorseId').val(); // Assuming you have hidden input with horseId

            // Send AJAX request to update the database
            $.ajax({
                url: 'update_horse_details.php',
                type: 'POST',
                data: {
                    horseId: horseId,
                    YEARFOAL: updatedYearFoal,
                    SEX: updatedSex,
                    Sire: updatedSire,
                    DAM: updatedDam
                },
                success: function(response) {
                    try {
                        // If response is a string, try parsing it
                        if (typeof response === 'string') {
                            response = JSON.parse(response);
                        }
                        console.log('Backend response:', response); // Log the whole response to check its structure

                        if (response.success) {
                            alert('Horse details updated successfully.');

                            // Update the display text with new values
                            $('#yearFoalDisplay').text(updatedYearFoal);
                            $('#sexDisplay').text(updatedSex);
                            $('#sireDisplay').text(updatedSire);
                            $('#damDisplay').text(updatedDam);

                            // Hide input fields and show updated text
                            $('#yearFoalInput').hide();
                            $('#sexInput').hide();
                            $('#sireInput').hide();
                            $('#damInput').hide();
                            $('#yearFoalDisplay').show();
                            $('#sexDisplay').show();
                            $('#sireDisplay').show();
                            $('#damDisplay').show();

                            // Show Edit button and hide Save/Cancel buttons
                            $('#editBtn').show();
                            $('#saveBtn').hide();
                            $('#cancelBtn').hide();
                        } else {
                            alert('Failed to update horse details.');
                        }
                    } catch (e) {
                        console.error('Response parsing error:', e);
                        alert('An error occurred while updating details.');
                    }
                }
            });
        });

        $(document).ready(function() {
            // Create modal dialog for column selector
            const modalHTML = `
    <div id="columnSelectorModal" class="modal">
        <div class="modal-content">
            <span class="close-modal">&times;</span>
            <h3>Select Columns to Display</h3>
            <form id="columnSelectorForm">
                ${$('#columnCheckboxes').html()}
                <button type="submit" class="btn btn-primary" style="margin-top:10px">Save Preferences</button>
            </form>
        </div>
    </div>`;

            $('body').append(modalHTML);

            // Open modal when button is clicked
            $('#columnSelectorBtn').click(function() {
                $('#columnSelectorModal').show();
            });

            // Close modal when X is clicked
            $('.close-modal').click(function() {
                $('#columnSelectorModal').hide();
            });

            // Close modal when clicking outside
            $(window).click(function(event) {
                if (event.target.id === 'columnSelectorModal') {
                    $('#columnSelectorModal').hide();
                }
            });

            // Handle form submission
            // Handle form submission
            $('#columnSelectorForm').on('submit', function(e) {
                e.preventDefault();

                // Collect all checkbox values as booleans
                var columnPrefs = {};
                $('input[name^="column_prefs["]').each(function() {
                    var colName = $(this).attr('name').match(/\[(.*?)\]/)[1];
                    columnPrefs[colName] = $(this).is(':checked');
                });

                // Submit via AJAX
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: {
                        column_prefs: columnPrefs
                    },
                    success: function() {
                        // Force reload to ensure changes are applied
                        window.location.reload(true);
                    },
                    error: function(xhr, status, error) {
                        console.error('Error saving preferences:', error);
                        alert('Error saving column preferences');
                    }
                });
            });
        });
    </script>

</body>

</html>