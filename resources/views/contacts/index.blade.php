<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Intricare CRM</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .modal-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1040;
            width: 100vw;
            height: 100vh;
            background-color: #000;
        }
        .modal-backdrop.fade { opacity: 0; }
        .modal-backdrop.show { opacity: 0.5; }
        body.modal-open {
            overflow: hidden;
            padding-right: 0 !important;
        }
        .contact-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
        }
        .drag-handle {
            cursor: move;
            opacity: 0.6;
        }
        .dragging {
            opacity: 0.5;
            background-color: #f8f9fa;
        }
    </style>
</head>

<body class="bg-light p-4">
    <div class="container">
        <h2 class="mb-4">Intricare CRM</h2>

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary mb-3" id="addContactBtn" data-bs-toggle="modal" data-bs-target="#contactModal">
                Add Contact
            </button>
        </div>

        <div class="card p-3 mb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" id="filterName" class="form-control" placeholder="Name">
                </div>
                <div class="col-md-3">
                    <input type="email" id="filterEmail" class="form-control" placeholder="Email">
                </div>
                <div class="col-md-3">
                    <select id="filterGender" class="form-select">
                        <option value="">Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-dark w-100" id="btnFilter">Search</button>
                </div>
            </div>
        </div>

        <div class="card p-3">
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            @foreach($customFields as $field)
                            <th>{{ $field->label }}</th>
                            @endforeach
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="contactsTable"></tbody>
                </table>
            </div>
            <div id="paginationLinks" class="d-flex justify-content-center mt-3"></div>
        </div>
    </div>

    <!-- Contact Modal -->
    <div class="modal fade" id="contactModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Contact</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="contactForm" enctype="multipart/form-data">
                        <input type="hidden" name="contact_id" id="contact_id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Name</label>
                                <input type="text" name="name" id="name" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" id="email" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input type="text" name="phone" id="phone" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select name="gender" id="gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Profile Image</label>
                                <input type="file" name="profile_image" id="profile_image" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Additional File</label>
                                <input type="file" name="additional_file" id="additional_file" class="form-control">
                            </div>

                            <div class="col-12">
                                <h5 class="mt-3">Custom Fields</h5>
                                <div class="d-flex justify-content-end mb-2">
                                    <button type="button" class="btn btn-sm btn-secondary" id="manageFieldsBtn">
                                        Manage Custom Fields
                                    </button>
                                </div>
                                <div id="customFieldsArea" class="row g-3 border-top pt-3">
                                    @foreach($customFields as $field)
                                    <div class="col-md-6">
                                        <label class="form-label">{{ $field->label }}</label>
                                        <input type="text" class="form-control custom-field"
                                            name="custom_fields[{{ $field->id }}]"
                                            data-id="{{ $field->id }}">
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button id="saveContactBtn" class="btn btn-primary">Save Contact</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Custom Fields Modal -->
    <div class="modal fade" id="customFieldsModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Manage Custom Fields</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h5>Add New Field</h5>
                    <form id="newCustomFieldForm" class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Field Label</label>
                            <input type="text" name="label" class="form-control" required
                                placeholder="Enter field display name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Field Type</label>
                            <select name="type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="text">Text</option>
                                <option value="email">Email</option>
                                <option value="phone">Phone</option>
                                <option value="number">Number</option>
                                <option value="date">Date</option>
                                <option value="textarea">Text Area</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Display Order</label>
                            <input type="number" name="sort_order" class="form-control"
                                value="0" min="0" placeholder="Order">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Requirement</label>
                            <select name="is_required" class="form-select">
                                <option value="0">Optional</option>
                                <option value="1">Required</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-success w-100">Add Field</button>
                        </div>
                    </form>

                    <h5>Existing Fields</h5>
                    <div class="mb-2">
                        <small class="text-muted">Drag to reorder fields</small>
                    </div>
                    <ul class="list-group sortable-fields" id="existingFieldsList"></ul>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Merge Modal -->
    <div class="modal fade" id="mergeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Merge Contacts</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Select the master contact (the primary record to keep):</p>
                    <div id="mergeCandidates"></div>
                    <hr>
                    <div id="mergePreviewArea" style="display:none;"></div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button id="btnPreviewMerge" class="btn btn-outline-primary">Preview</button>
                    <button id="btnConfirmMerge" class="btn btn-primary" disabled>Confirm Merge</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Merge Success Modal -->
    <div class="modal fade" id="mergeSuccessModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Merge Completed</h5>
                    <button class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="mergeSuccessBody"></div>
                <div class="modal-footer">
                    <button class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // App data
        var fieldList = @json($customFields);
        var mergeContactId = null;
        var storageBaseUrl = "{{ Storage::url('') }}";
        var mergeModal = null;
        var successModal = null;

        // Helper functions
        function showAlert(type, msg) {
            alert(msg);
        }

        function toggleSpinner(show) {
            if (show) {
                $('body').append('<div id="loadingOverlay" style="position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,0.7);display:flex;align-items:center;justify-content:center;z-index:2000;"><div class="spinner-border"></div></div>');
            } else {
                $('#loadingOverlay').remove();
            }
        }

        function clearErrors() {
            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();
        }

        function showErrors(errorData) {
            clearErrors();

            $.each(errorData, function(fieldName, messages) {
                var inputField = $('[name="' + fieldName + '"]');
                if (!inputField.length && fieldName.startsWith("custom_fields.")) {
                    var fieldId = fieldName.split(".")[1];
                    inputField = $('[data-id="' + fieldId + '"]');
                }
                if (inputField.length) {
                    inputField.addClass('is-invalid');
                    inputField.after('<div class="invalid-feedback">' + messages.join('<br>') + '</div>');
                }
            });
        }

        function resetForm() {
            $('#modalTitle').text('Add Contact');
            $('#contactForm')[0].reset();
            $('#contact_id').val('');
            $('#profilePreview').remove();
            clearErrors();
            $('.custom-field').val('');
        }

        function cleanupModals() {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('.modal').removeClass('show').hide();
        }

        // Modal setup
        function setupModals() {
            mergeModal = new bootstrap.Modal(document.getElementById('mergeModal'));
            successModal = new bootstrap.Modal(document.getElementById('mergeSuccessModal'));

            $('#contactModal').on('show.bs.modal', function(e) {
                var trigger = e.relatedTarget;
                if (trigger && trigger.id === 'addContactBtn') {
                    resetForm();
                }
            });

            $('#contactModal').on('hidden.bs.modal', function() {
                cleanupModals();
            });

            $('#mergeModal').on('hidden.bs.modal', function() {
                mergeContactId = null;
                $('#mergePreviewArea').hide().html('');
                $('#btnConfirmMerge').prop('disabled', true);
                $('#mergeCandidates').html('');
                cleanupModals();
            });

            $('#mergeSuccessModal').on('hidden.bs.modal', function() {
                cleanupModals();
            });

            $(document).keyup(function(e) {
                if (e.keyCode === 27) cleanupModals();
            });
        }

        // Contact functions
        function fetchContacts(url) {
            if (!url) url = "{{ url('/contacts') }}";

            toggleSpinner(true);

            var searchData = {
                name: $('#filterName').val(),
                email: $('#filterEmail').val(),
                gender: $('#filterGender').val()
            };

            $.ajax({
                url: url,
                method: "GET",
                data: searchData,
                success: function(res) {
                    fillContactsTable(res.data);
                    setupPagination(res.links);
                },
                error: function() {
                    showAlert('error', 'Could not load contacts');
                },
                complete: function() {
                    toggleSpinner(false);
                }
            });
        }

        function fillContactsTable(contacts) {
            var html = '';

            if (contacts.length === 0) {
                html = '<tr><td colspan="' + (6 + fieldList.length) + '" class="text-center">No contacts found</td></tr>';
            } else {
                // Use sorted field order for consistent columns
                var sortedFields = [...fieldList].sort(function(a, b) {
                    return (a.sort_order || 0) - (b.sort_order || 0);
                });
                
                contacts.forEach(function(contact) {
                    var imgSrc = contact.profile_image ? storageBaseUrl + contact.profile_image : 'https://via.placeholder.com/50';

                    var customCells = '';
                    sortedFields.forEach(function(field) {
                        var customVal = (contact.custom_values || []).find(function(v) {
                            return v.custom_field_id == field.id;
                        });
                        customCells += '<td>' + (customVal ? customVal.value : '') + '</td>';
                    });

                    var statusHtml = contact.is_active === false ?
                        '<span class="badge bg-secondary">Merged</span>' :
                        '<span class="badge bg-success">Active</span>';

                    var actionButtons = '';
                    if (contact.is_active) {
                        actionButtons = '<button class="btn btn-sm btn-info mergeBtn" data-id="' + contact.id + '">Merge</button> ' +
                            '<button class="btn btn-warning btn-sm editBtn" data-id="' + contact.id + '">Edit</button> ' +
                            '<button class="btn btn-sm btn-danger deleteBtn" data-id="' + contact.id + '">Delete</button>';
                    } else {
                        actionButtons = '<span class="text-muted">Merged</span>';
                    }

                    html += '<tr>' +
                        '<td><img src="' + imgSrc + '" class="contact-image img-thumbnail"></td>' +
                        '<td>' + contact.name + '</td>' +
                        '<td>' + (contact.email || "") + '</td>' +
                        '<td>' + (contact.phone || "") + '</td>' +
                        customCells +
                        '<td>' + statusHtml + '</td>' +
                        '<td>' + actionButtons + '</td>' +
                        '</tr>';
                });
            }

            $('#contactsTable').html(html);
        }

        function setupPagination(links) {
            $('#paginationLinks').html(links || '');
        }

        function removeContact(contactId) {
            if (!confirm('Delete this contact?')) return;

            toggleSpinner(true);

            $.ajax({
                url: '/contacts/' + contactId,
                method: 'POST',
                data: {
                    _method: 'DELETE',
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    showAlert('success', res.message || 'Contact deleted');
                    fetchContacts();
                },
                error: function() {
                    showAlert('error', 'Delete failed');
                },
                complete: function() {
                    toggleSpinner(false);
                }
            });
        }

        function loadContactForEdit(contactId) {
            toggleSpinner(true);

            $.ajax({
                url: '/contacts/' + contactId,
                method: "GET",
                success: function(contact) {
                    resetForm();
                    fillEditForm(contact);
                    new bootstrap.Modal(document.getElementById('contactModal')).show();
                },
                error: function() {
                    showAlert('error', 'Could not load contact');
                },
                complete: function() {
                    toggleSpinner(false);
                }
            });
        }

        function fillEditForm(contact) {
            $('#modalTitle').text('Edit Contact');
            $('#contact_id').val(contact.id);
            $('#name').val(contact.name);
            $('#email').val(contact.email);
            $('#phone').val(contact.phone);
            $('#gender').val(contact.gender);

            $('.custom-field').each(function() {
                var fieldId = $(this).data('id');
                var fieldValue = contact.custom_values.find(function(v) {
                    return v.custom_field_id == fieldId;
                });
                $(this).val(fieldValue ? fieldValue.value : "");
            });

            if (contact.profile_image) {
                $('#profile_image').after('<img id="profilePreview" class="img-thumbnail mt-2" style="max-width:120px;" src="' + storageBaseUrl + contact.profile_image + '">');
            }
        }

        function saveContactData() {
            var contactId = $('#contact_id').val();
            var formUrl = contactId ? '/contacts/' + contactId : '{{ route("contacts.store") }}';
            var formData = new FormData(document.getElementById('contactForm'));

            if (contactId) {
                formData.append('_method', 'PUT');
            }

            toggleSpinner(true);
            clearErrors();

            $.ajax({
                url: formUrl,
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(res) {
                    showAlert('success', res.message || "Saved!");
                    var modal = bootstrap.Modal.getInstance(document.getElementById('contactModal'));
                    if (modal) modal.hide();
                    fetchContacts();
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        showErrors(xhr.responseJSON.errors);
                    } else {
                        showAlert('error', 'Save failed');
                    }
                },
                complete: function() {
                    toggleSpinner(false);
                }
            });
        }

        // Custom fields functions
        function openFieldManager() {
            loadFieldsList();
        }

        function loadFieldsList() {
            toggleSpinner(true);

            $.ajax({
                url: '/custom-fields',
                method: 'GET',
                success: function(fields) {
                    displayFields(fields);
                    new bootstrap.Modal(document.getElementById('customFieldsModal')).show();
                },
                error: function() {
                    showAlert('error', 'Could not load fields');
                },
                complete: function() {
                    toggleSpinner(false);
                }
            });
        }

        function displayFields(fields) {
            var html = '';

            if (fields.length === 0) {
                html = '<li class="list-group-item">No custom fields yet.</li>';
            } else {
                // Sort fields by order before displaying
                fields.sort(function(a, b) {
                    return (a.sort_order || 0) - (b.sort_order || 0);
                }).forEach(function(field) {
                    var requirementBadge = field.is_required ?
                        '<span class="badge bg-danger ms-2">Required</span>' :
                        '<span class="badge bg-secondary ms-2">Optional</span>';

                    // Show the original UI input type if provided in meta, otherwise stored type
                    var displayType = (field.meta && field.meta.input_type) ? field.meta.input_type : field.type;
                    var typeBadge = '<span class="badge bg-info ms-2">' + (displayType || 'text') + '</span>';
                    var orderBadge = '<span class="badge bg-light text-dark ms-2">#' + (field.sort_order || 0) + '</span>';

                    html += '<li class="list-group-item d-flex justify-content-between align-items-center" ' +
                        'data-field-id="' + field.id + '" draggable="true">' +
                        '<div class="d-flex align-items-center">' +
                        '<span class="drag-handle me-2">↕</span>' +
                        '<div>' +
                        field.label +
                        typeBadge +
                        requirementBadge +
                        orderBadge +
                        '</div>' +
                        '</div>' +
                        '<button class="btn btn-danger btn-sm delete-field-btn" data-id="' + field.id + '">Delete</button>' +
                        '</li>';
                });
            }

            $('#existingFieldsList').html(html);
        }

        function createNewField() {
            var fieldLabel = $('#newCustomFieldForm [name="label"]').val();
            var fieldType = $('#newCustomFieldForm [name="type"]').val();
            var isRequired = $('#newCustomFieldForm [name="is_required"]').val();
            var sortOrder = parseInt($('#newCustomFieldForm [name="sort_order"]').val()) || 0;

            if (!fieldLabel || !fieldType) {
                showAlert('error', 'Please fill all required fields');
                return;
            }

            toggleSpinner(true);

            $.ajax({
                url: '/custom-fields',
                method: 'POST',
                data: {
                    label: fieldLabel,
                    type: fieldType,
                    is_required: isRequired,
                    sort_order: sortOrder,
                    _token: '{{ csrf_token() }}'
                },
                success: function(newField) {
                    showAlert('success', 'Field added successfully!');
                    fieldList.push(newField);
                    
                    // Refresh everything
                    refreshContactFormFields();
                    updateTableHeaders();
                    fetchContacts();
                    
                    // Close modal and reset form
                    bootstrap.Modal.getInstance(document.getElementById('customFieldsModal')).hide();
                    $('#newCustomFieldForm')[0].reset();
                    toggleSpinner(false);
                },
                error: function(xhr) {
                    showAlert('error', xhr.responseJSON?.message || 'Failed to add field');
                    toggleSpinner(false);
                }
            });
        }

        function addFieldToForm(field) {
            var fieldInput = '';
            // Prefer the original UI type saved in meta; fallback to canonical stored type
            var uiType = (field.meta && field.meta.input_type) ? field.meta.input_type : field.type;

            switch (uiType) {
                case 'textarea':
                    fieldInput = '<textarea class="form-control custom-field" ' +
                        'name="custom_fields[' + field.id + ']" ' +
                        'data-id="' + field.id + '" ' +
                        'rows="3"' +
                        (field.is_required ? ' required' : '') + '></textarea>';
                    break;
                case 'email':
                    fieldInput = '<input type="email" class="form-control custom-field" ' +
                        'name="custom_fields[' + field.id + ']" ' +
                        'data-id="' + field.id + '" ' +
                        (field.is_required ? ' required' : '') + '>';
                    break;
                case 'number':
                    fieldInput = '<input type="number" class="form-control custom-field" ' +
                        'name="custom_fields[' + field.id + ']" ' +
                        'data-id="' + field.id + '" ' +
                        (field.is_required ? ' required' : '') + '>';
                    break;
                case 'date':
                    fieldInput = '<input type="date" class="form-control custom-field" ' +
                        'name="custom_fields[' + field.id + ']" ' +
                        'data-id="' + field.id + '" ' +
                        (field.is_required ? ' required' : '') + '>';
                    break;
                default: // text, phone
                    fieldInput = '<input type="text" class="form-control custom-field" ' +
                        'name="custom_fields[' + field.id + ']" ' +
                        'data-id="' + field.id + '" ' +
                        (field.is_required ? ' required' : '') + '>';
            }

            $('#customFieldsArea').append(
                '<div class="col-md-6" data-field-id="' + field.id + '">' +
                '<label class="form-label">' + field.label +
                (field.is_required ? '<span class="text-danger"> *</span>' : '') +
                '</label>' +
                fieldInput +
                '</div>'
            );
        }

        function deleteField(fieldId) {
            if (!confirm('Are you sure? This will remove all data for this field.')) return;

            toggleSpinner(true);

            $.ajax({
                url: '/custom-fields/' + fieldId,
                method: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(res) {
                    showAlert('success', res.message || 'Field deleted!');
                    fieldList = fieldList.filter(function(f) {
                        return f.id != fieldId;
                    });
                    $('[data-field-id="' + fieldId + '"]').fadeOut(300, function() {
                        $(this).remove();
                    });
                    fetchContacts();
                    toggleSpinner(false);
                },
                error: function() {
                    showAlert('error', 'Delete failed');
                    toggleSpinner(false);
                }
            });
        }

        // Function to refresh contact form fields in correct order
        function refreshContactFormFields() {
            // Clear existing custom fields
            $('#customFieldsArea').html('');

            // Add fields in sorted order
            fieldList.sort(function(a, b) {
                return (a.sort_order || 0) - (b.sort_order || 0);
            }).forEach(function(field) {
                addFieldToForm(field);
            });
        }

        function updateTableHeaders() {
            var headerRow = $('#contactsTable').closest('table').find('thead tr');
            var tbody = $('#contactsTable');
            
            // Remove existing custom field headers and data cells
            headerRow.find('th:gt(3):not(:last):not(:nth-last-child(2))').remove();
            tbody.find('tr').each(function() {
                $(this).find('td:gt(3):not(:last):not(:nth-last-child(2))').remove();
            });
            
            // Add new custom field headers in correct order
            var sortedFields = [...fieldList].sort(function(a, b) {
                return (a.sort_order || 0) - (b.sort_order || 0);
            });

            sortedFields.forEach(function(field) {
                $('<th></th>').text(field.label).insertBefore(headerRow.find('th:nth-last-child(2)'));
            });
        }

        // Drag and drop functionality
        function initSortableFields() {
            $('#existingFieldsList').on('dragstart', '.list-group-item', function(e) {
                e.originalEvent.dataTransfer.setData('text/plain', $(this).data('field-id'));
                $(this).addClass('dragging');
            });

            $('#existingFieldsList').on('dragend', '.list-group-item', function() {
                $(this).removeClass('dragging');
            });

            $('#existingFieldsList').on('dragover', function(e) {
                e.preventDefault();
                var afterElement = getDragAfterElement(this, e.originalEvent.clientY);
                var draggable = $('.dragging')[0];
                if (afterElement == null) {
                    this.appendChild(draggable);
                } else {
                    this.insertBefore(draggable, afterElement);
                }
            });

            $('#existingFieldsList').on('drop', function(e) {
                e.preventDefault();
                updateFieldOrder();
            });
        }

        function getDragAfterElement(container, y) {
            var draggableElements = [...container.querySelectorAll('.list-group-item:not(.dragging)')];

            return draggableElements.reduce((closest, child) => {
                var box = child.getBoundingClientRect();
                var offset = y - box.top - box.height / 2;
                if (offset < 0 && offset > closest.offset) {
                    return { offset: offset, element: child };
                } else {
                    return closest;
                }
            }, { offset: Number.NEGATIVE_INFINITY }).element;
        }

        function updateFieldOrder() {
            var newOrder = [];
            $('#existingFieldsList .list-group-item').each(function(index) {
                var fieldId = $(this).data('field-id');
                newOrder.push({
                    id: fieldId,
                    sort_order: index
                });
            });

            toggleSpinner(true);

            $.ajax({
                url: '/custom-fields/update-order',
                method: 'POST',
                data: {
                    order: newOrder,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    // Update fieldList with new order from server
                    if (response.fields) {
                        fieldList = response.fields;
                    } else {
                        // Fallback: update local fieldList order
                        newOrder.forEach(function(item) {
                            var field = fieldList.find(function(f) { return f.id == item.id; });
                            if (field) field.sort_order = item.sort_order;
                        });
                        fieldList.sort(function(a, b) { return (a.sort_order || 0) - (b.sort_order || 0); });
                    }
                    
                    // Refresh everything
                    refreshContactFormFields();
                    updateTableHeaders();
                    fetchContacts();
                    
                    showAlert('success', 'Field order updated successfully!');
                    toggleSpinner(false);
                },
                error: function(xhr) {
                    showAlert('error', 'Failed to update field order');
                    toggleSpinner(false);
                    loadFieldsList(); // Reset to server order
                }
            });
        }

        // Merge functions
        function startMerge(secondaryId) {
            mergeContactId = secondaryId;
            $('#mergePreviewArea').hide().html('');
            $('#btnConfirmMerge').prop('disabled', true);

            $.get('/contacts/merge/exclude/' + mergeContactId, function(contacts) {
                showMergeContacts(contacts);
                if (mergeModal) {
                    mergeModal.show();
                } else {
                    new bootstrap.Modal(document.getElementById('mergeModal')).show();
                }
            });
        }

        function showMergeContacts(contacts) {
            var html = '<div class="list-group">';

            if (contacts.length === 0) {
                html += '<div class="alert alert-warning">No other active contacts available for merging.</div>';
            } else {
                contacts.forEach(function(contact) {
                    html += '<label class="list-group-item">' +
                        '<input type="radio" name="master_id" value="' + contact.id + '" required />' +
                        '<strong>' + contact.name + '</strong> ' + (contact.email ? '(' + contact.email + ')' : '') +
                        '</label>';
                });
            }

            html += '</div>';
            $('#mergeCandidates').html(html);
        }

        function showMergePreview() {
            var masterId = $('#mergeCandidates input[name="master_id"]:checked').val();
            if (!masterId) return alert('Please select a master contact');

            $.post('{{ route("contacts.merge.preview") }}', {
                master_id: masterId,
                secondary_id: mergeContactId,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {
                displayPreview(res.preview);
                $('#btnConfirmMerge').prop('disabled', false);
            }).fail(function(xhr) {
                alert('Preview failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
            });
        }

        function displayPreview(preview) {
            var html = '<div class="merge-preview">';

            if (preview.main_fields && preview.main_fields.length > 0) {
                html += '<h6>Main Fields:</h6><ul class="list-group mb-3">';
                preview.main_fields.forEach(function(field) {
                    var bgClass = field.action === 'concatenate' ? 'list-group-item-warning' : 'list-group-item-success';
                    html += '<li class="list-group-item ' + bgClass + '">' +
                        '<strong>' + field.field_label + '</strong><br>' +
                        'Master: ' + (field.master_value || 'Empty') + ' + Secondary: ' + field.secondary_value + '<br>' +
                        '<small>→ Will become: ' + field.new_value + '</small>' +
                        '</li>';
                });
                html += '</ul>';
            }

            if (preview.emails && preview.emails.length > 0) {
                html += '<h6>Email Changes:</h6><ul class="list-group mb-3">';
                preview.emails.forEach(function(email) {
                    if (email.action === 'add_new') {
                        html += '<li class="list-group-item list-group-item-success">' +
                            '<strong>New Email:</strong> ' + email.email + '<br>' +
                            '<small>Action: Will be added as new email</small>' +
                            '</li>';
                    } else if (email.action === 'concatenate') {
                        html += '<li class="list-group-item list-group-item-warning">' +
                            '<strong>Email Concatenation:</strong><br>' +
                            'Existing: ' + email.existing_email + ' + New: ' + email.email + '<br>' +
                            '<small>→ Will become: ' + email.new_value + '</small>' +
                            '</li>';
                    }
                });
                html += '</ul>';
            }

            if (preview.phones && preview.phones.length > 0) {
                html += '<h6>Phone Changes:</h6><ul class="list-group mb-3">';
                preview.phones.forEach(function(phone) {
                    if (phone.action === 'add_new') {
                        html += '<li class="list-group-item list-group-item-success">' +
                            '<strong>New Phone:</strong> ' + phone.phone + '<br>' +
                            '<small>Action: Will be added as new phone</small>' +
                            '</li>';
                    } else if (phone.action === 'concatenate') {
                        html += '<li class="list-group-item list-group-item-warning">' +
                            '<strong>Phone Concatenation:</strong><br>' +
                            'Existing: ' + phone.existing_phone + ' + New: ' + phone.phone + '<br>' +
                            '<small>→ Will become: ' + phone.new_value + '</small>' +
                            '</li>';
                    }
                });
                html += '</ul>';
            }

            if (preview.custom_fields && preview.custom_fields.length > 0) {
                html += '<h6>Custom Field Changes:</h6><ul class="list-group">';
                preview.custom_fields.forEach(function(field) {
                    var bgClass = field.action === 'copy_to_master' ? 'list-group-item-success' :
                        field.action === 'concatenate' ? 'list-group-item-warning' : 'list-group-item-info';

                    if (field.action === 'copy_to_master') {
                        html += '<li class="list-group-item ' + bgClass + '">' +
                            '<strong>' + field.field_label + '</strong><br>' +
                            'Master: Empty → Secondary: ' + field.secondary_value + '<br>' +
                            '<small>Action: Will be copied to master</small>' +
                            '</li>';
                    } else if (field.action === 'concatenate') {
                        html += '<li class="list-group-item ' + bgClass + '">' +
                            '<strong>' + field.field_label + '</strong><br>' +
                            'Master: ' + field.master_value + ' + Secondary: ' + field.secondary_value + '<br>' +
                            '<small>→ Will become: ' + field.new_value + '</small>' +
                            '</li>';
                    }
                });
                html += '</ul>';
            }

            if ((!preview.main_fields || preview.main_fields.length === 0) &&
                (!preview.emails || preview.emails.length === 0) &&
                (!preview.phones || preview.phones.length === 0) &&
                (!preview.custom_fields || preview.custom_fields.length === 0)) {
                html += '<div class="alert alert-info">No new data to merge. Contacts are identical.</div>';
            }

            html += '</div>';
            $('#mergePreviewArea').html(html).show();
        }

        function doMerge() {
            var masterId = $('#mergeCandidates input[name="master_id"]:checked').val();
            toggleSpinner(true);

            $.post('{{ route("contacts.merge.perform") }}', {
                master_id: masterId,
                secondary_id: mergeContactId,
                _token: $('meta[name="csrf-token"]').attr('content')
            }, function(res) {
                toggleSpinner(false);
                showMergeResult(res);
                fetchContacts();
                if (mergeModal) mergeModal.hide();
            }).fail(function(xhr) {
                toggleSpinner(false);
                alert('Merge failed: ' + (xhr.responseJSON?.message || 'Unknown error'));
            });
        }

        function showMergeResult(response) {
            var merged = response.merged_details || [];
            var rows = '';

            if (merged.length === 0) {
                rows = '<tr><td colspan="5" class="text-center">No fields were merged</td></tr>';
            } else {
                merged.forEach(function(item) {
                    var fieldDisplay = item.field;
                    if (item.field.startsWith('Custom Field')) {
                        var fieldId = item.field.replace('Custom Field ', '');
                        var customField = fieldList.find(function(cf) {
                            return cf.id == fieldId;
                        });
                        fieldDisplay = customField ? customField.label : item.field;
                    }

                    rows += '<tr>' +
                        '<td>' + fieldDisplay + '</td>' +
                        '<td>' + (item.master_value || '-') + '</td>' +
                        '<td>' + (item.secondary_value || '-') + '</td>' +
                        '<td>' + (item.final_value || '-') + '</td>' +
                        '<td>' + item.action + '</td>' +
                        '</tr>';
                });
            }

            $("#mergeSuccessBody").html(
                '<p class="mb-2"><strong>Contacts merged successfully!</strong></p>' +
                '<p>Master: <strong>' + response.master_name + '</strong> | Secondary: <strong>' + response.secondary_name + '</strong></p>' +
                '<div class="table-responsive">' +
                '<table class="table table-bordered table-striped">' +
                '<thead>' +
                '<tr>' +
                '<th>Field</th>' +
                '<th>Master Value</th>' +
                '<th>Secondary Value</th>' +
                '<th>Final Value</th>' +
                '<th>Action Taken</th>' +
                '</tr>' +
                '</thead>' +
                '<tbody>' + rows + '</tbody>' +
                '</table>' +
                '</div>'
            );

            if (successModal) {
                successModal.show();
            } else {
                new bootstrap.Modal(document.getElementById('mergeSuccessModal')).show();
            }
        }

        // Page setup
        $(document).ready(function() {
            setupModals();
            fetchContacts();
            initSortableFields();

            $('#profile_image').change(function() {
                var file = this.files[0];
                if (!file) return;

                var reader = new FileReader();
                reader.onload = function(e) {
                    if (!$('#profilePreview').length) {
                        $('#profile_image').after('<img id="profilePreview" class="img-thumbnail mt-2" style="max-width:120px;">');
                    }
                    $('#profilePreview').attr('src', e.target.result);
                };
                reader.readAsDataURL(file);
            });

            $(document).on('click', '#btnFilter', function() {
                fetchContacts();
            });

            $(document).on('click', '.editBtn', function() {
                loadContactForEdit($(this).data('id'));
            });

            $(document).on('click', '.deleteBtn', function() {
                removeContact($(this).data('id'));
            });

            $(document).on('click', '#saveContactBtn', function(e) {
                e.preventDefault();
                saveContactData();
            });

            $(document).on('click', '#manageFieldsBtn', function() {
                openFieldManager();
            });

            $(document).on('submit', '#newCustomFieldForm', function(e) {
                e.preventDefault();
                createNewField();
            });

            $(document).on('click', '.delete-field-btn', function() {
                deleteField($(this).data('id'));
            });

            $(document).on('click', '.mergeBtn', function() {
                startMerge($(this).data('id'));
            });

            $(document).on('click', '#btnPreviewMerge', function() {
                showMergePreview();
            });

            $(document).on('click', '#btnConfirmMerge', function() {
                doMerge();
            });
        });
    </script>
</body>
</html>