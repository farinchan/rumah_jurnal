@extends('back.app')
@section('styles')
    <style>
        .certificate-editor-container {
            position: relative;
            width: 100%;
            max-width: 100%;
            overflow: auto;
        }

        .certificate-canvas {
            position: relative;
            margin: 0 auto;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            border-radius: 4px;
            overflow: hidden;
            font-family: Arial, Helvetica, sans-serif;
        }

        .certificate-canvas.landscape {
            width: 842px;
            /* A4 landscape at 96 DPI */
            height: 595px;
        }

        .certificate-canvas.portrait {
            width: 595px;
            height: 842px;
        }

        .certificate-background {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .text-element {
            position: absolute;
            cursor: move;
            border: none;
            border-radius: 4px;
            transition: border-color 0.2s, background-color 0.2s;
            user-select: none;
            white-space: nowrap;
            line-height: 1;
        }

        .text-element:hover {
            outline: 2px dashed #009ef7;
            background-color: rgba(0, 158, 247, 0.1);
        }

        .text-element.active {
            outline: 2px dashed #009ef7;
            background-color: rgba(0, 158, 247, 0.15);
            z-index: 100;
        }

        .text-element.dragging {
            opacity: 0.8;
            z-index: 1000;
        }

        .signature-element {
            position: absolute;
            cursor: move;
            border: none;
            border-radius: 4px;
            transition: border-color 0.2s;
        }

        .signature-element:hover,
        .signature-element.active {
            outline: 2px dashed #50cd89;
        }

        .signature-element img {
            width: 100%;
            height: auto;
            display: block;
        }

        .resize-handle {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #50cd89;
            border: 2px solid white;
            border-radius: 50%;
            cursor: nwse-resize;
            right: -6px;
            bottom: -6px;
            display: none;
        }

        .signature-element.active .resize-handle {
            display: block;
        }

        .element-controls {
            position: absolute;
            top: -30px;
            left: 0;
            display: none;
            gap: 5px;
        }

        .text-element.active .element-controls {
            display: flex;
        }

        .element-controls button {
            padding: 2px 6px;
            font-size: 10px;
        }

        .properties-panel {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
        }

        .property-group {
            margin-bottom: 15px;
        }

        .property-group label {
            font-weight: 500;
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 4px;
        }

        .save-indicator {
            position: fixed;
            bottom: 20px;
            right: 20px;
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .save-indicator.saving {
            background: #fff3cd;
            color: #856404;
        }

        .save-indicator.saved {
            background: #d4edda;
            color: #155724;
        }

        .save-indicator.hidden {
            opacity: 0;
            transform: translateY(20px);
        }

        .zoom-controls {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .available-elements {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 20px;
        }

        .available-element-btn {
            padding: 6px 12px;
            border: 1px dashed #ccc;
            border-radius: 6px;
            background: white;
            cursor: pointer;
            font-size: 12px;
            transition: all 0.2s;
        }

        .available-element-btn:hover {
            border-color: #009ef7;
            background: #f0f8ff;
        }

        .available-element-btn.added {
            border-color: #50cd89;
            background: #e8fff3;
        }
    </style>
@endsection

@section('content')
    @php
        [$before, $after] = explode(' - ', $event->datetime);
        $date_before = \Carbon\Carbon::parse($before)->toDateTimeString();
        $date_after = \Carbon\Carbon::parse($after)->toDateTimeString();
    @endphp
    <div id="kt_content_container" class="container-xxl">
        @include('back.pages.event.detail.header')

        <div class="card mb-5 mb-xl-10">
            <div class="card-header border-0">
                <div class="card-title m-0">
                    <h3 class="fw-bold m-0">Edit Template: {{ $template->name }}</h3>
                </div>
                <div class="card-toolbar d-flex gap-2">
                    <div class="zoom-controls me-3">
                        <button type="button" class="btn btn-sm btn-light" onclick="zoomOut()">
                            <i class="ki-outline ki-minus fs-4"></i>
                        </button>
                        <span id="zoomLevel">100%</span>
                        <button type="button" class="btn btn-sm btn-light" onclick="zoomIn()">
                            <i class="ki-outline ki-plus fs-4"></i>
                        </button>
                    </div>
                    <a href="{{ route('back.event.detail.certificate.preview', [$event->id, $template->id]) }}"
                        class="btn btn-sm btn-light-info" target="_blank">
                        <i class="ki-outline ki-eye fs-2"></i> Preview PDF
                    </a>
                    <button type="button" class="btn btn-sm btn-primary" onclick="saveTemplate()">
                        <i class="ki-outline ki-check fs-2"></i> Simpan
                    </button>
                    <button type="button" class="btn btn-sm btn-light-danger" onclick="confirmDelete()">
                        <i class="ki-outline ki-trash fs-2"></i> Hapus Template
                    </button>
                </div>
            </div>
            <div class="card-body p-6">
                <div class="row">
                    <!-- Left Panel - Properties -->
                    <div class="col-lg-3 mb-5 mb-lg-0">
                        <div class="properties-panel sticky-top" style="top: 100px;">
                            <h5 class="fw-bold mb-4">Elemen Tersedia</h5>
                            <div class="available-elements" id="availableElements">
                                <button type="button" class="available-element-btn" data-element="certificate_number">
                                    <i class="ki-outline ki-hashtag-up fs-6"></i> Nomor Sertifikat
                                </button>
                                <button type="button" class="available-element-btn" data-element="participant_name">
                                    <i class="ki-outline ki-user fs-6"></i> Nama Peserta
                                </button>
                                <button type="button" class="available-element-btn" data-element="role_text">
                                    <i class="ki-outline ki-message-text fs-6"></i> Teks Peran
                                </button>
                                <button type="button" class="available-element-btn" data-element="event_name">
                                    <i class="ki-outline ki-calendar fs-6"></i> Nama Event
                                </button>
                                <button type="button" class="available-element-btn" data-element="event_date">
                                    <i class="ki-outline ki-time fs-6"></i> Tanggal Event
                                </button>
                                <button type="button" class="available-element-btn" data-element="custom_text">
                                    <i class="ki-outline ki-text fs-6"></i> Teks Kustom
                                </button>
                            </div>

                            <div class="separator separator-dashed my-4"></div>

                            <h5 class="fw-bold mb-4">Properti Elemen</h5>
                            <div id="elementProperties" class="d-none">
                                <div class="property-group">
                                    <label>Label</label>
                                    <input type="text" id="propLabel" class="form-control form-control-sm" readonly>
                                </div>
                                <div class="property-group">
                                    <label>Teks/Placeholder</label>
                                    <input type="text" id="propPlaceholder" class="form-control form-control-sm">
                                </div>
                                <div class="property-group">
                                    <label>Ukuran Font (px)</label>
                                    <input type="number" id="propFontSize" class="form-control form-control-sm" min="8"
                                        max="72">
                                </div>
                                <div class="property-group">
                                    <label>Tebal Font</label>
                                    <select id="propFontWeight" class="form-select form-select-sm">
                                        <option value="normal">Normal</option>
                                        <option value="bold">Bold</option>
                                    </select>
                                </div>
                                <div class="property-group">
                                    <label>Warna</label>
                                    <input type="color" id="propColor" class="form-control form-control-sm form-control-color"
                                        style="width: 100%;">
                                </div>
                                <div class="property-group">
                                    <label>Posisi X (px)</label>
                                    <input type="number" id="propX" class="form-control form-control-sm">
                                </div>
                                <div class="property-group">
                                    <label>Posisi Y (px)</label>
                                    <input type="number" id="propY" class="form-control form-control-sm">
                                </div>
                                <div class="d-flex gap-2 mt-3">
                                    <button type="button" class="btn btn-sm btn-light-danger flex-grow-1"
                                        onclick="removeSelectedElement()">
                                        <i class="ki-outline ki-trash fs-6"></i> Hapus
                                    </button>
                                </div>
                            </div>

                            <!-- Signature Properties Panel -->
                            <div id="signatureProperties" class="d-none">
                                <div class="property-group">
                                    <label>Elemen</label>
                                    <input type="text" class="form-control form-control-sm" value="Tanda Tangan" readonly>
                                </div>
                                <div class="property-group">
                                    <label>Lebar (px)</label>
                                    <input type="number" id="propSigWidth" class="form-control form-control-sm" min="50" max="400">
                                </div>
                                <div class="property-group">
                                    <label>Posisi X (px)</label>
                                    <input type="number" id="propSigX" class="form-control form-control-sm">
                                </div>
                                <div class="property-group">
                                    <label>Posisi Y (px)</label>
                                    <input type="number" id="propSigY" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div id="noElementSelected" class="text-muted text-center py-4">
                                <i class="ki-outline ki-cursor fs-3x mb-3 d-block"></i>
                                Klik elemen pada canvas untuk mengedit propertinya
                            </div>

                            <div class="separator separator-dashed my-4"></div>

                            <h5 class="fw-bold mb-4">Template Settings</h5>
                            <form id="templateSettingsForm">
                                <div class="property-group">
                                    <label>Nama Template</label>
                                    <input type="text" name="name" class="form-control form-control-sm"
                                        value="{{ $template->name }}">
                                </div>
                                <div class="property-group">
                                    <label>Ganti Template Gambar</label>
                                    <input type="file" name="template_image" class="form-control form-control-sm"
                                        accept="image/*" onchange="previewNewTemplate(this)">
                                </div>
                                <div class="property-group">
                                    <label>Tanda Tangan</label>
                                    <input type="file" name="signature_image" class="form-control form-control-sm"
                                        accept="image/*" onchange="previewNewSignature(this)">
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Right Panel - Canvas -->
                    <div class="col-lg-9">
                        <div class="certificate-editor-container" id="editorContainer">
                            <div class="certificate-canvas {{ $template->orientation }}" id="certificateCanvas">
                                <img src="{{ $template->getTemplateImageUrl() }}" alt="Template"
                                    class="certificate-background" id="templateBackground">

                                <!-- Text elements will be rendered here -->
                                <div id="textElementsContainer"></div>

                                <!-- Signature element -->
                                @if ($template->signature_image || true)
                                    <div class="signature-element" id="signatureElement"
                                        style="left: {{ $template->signature_position['x'] ?? 125 }}px; top: {{ $template->signature_position['y'] ?? 400 }}px; width: {{ $template->signature_position['width'] ?? 200 }}px;">
                                        <img src="{{ $template->getSignatureImageUrl() }}" alt="Signature"
                                            id="signatureImg">
                                        <div class="resize-handle" id="signatureResizeHandle"></div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Indicator -->
    <div class="save-indicator hidden" id="saveIndicator">
        <i class="ki-outline ki-check-circle fs-4 me-2"></i>
        <span id="saveIndicatorText">Tersimpan</span>
    </div>
@endsection

@section('scripts')
    <script>
        // Data from server
        let textElements = @json($template->text_elements ?? []);
        let signaturePosition = @json($template->signature_position ?? ['x' => 125, 'y' => 400, 'width' => 200]);
        let selectedSignature = false;
        let selectedElement = null;
        let zoomLevel = 1;
        let isDragging = false;
        let dragOffset = {
            x: 0,
            y: 0
        };
        let autoSaveTimeout = null;

        // Default elements configuration
        const defaultElements = {
            certificate_number: {
                id: 'certificate_number',
                label: 'Nomor Sertifikat',
                placeholder: 'Nomor: B-0001/ln.26/HM.00/01/2026',
                fontSize: 15,
                fontWeight: 'normal',
                color: '#424242',
            },
            participant_name: {
                id: 'participant_name',
                label: 'Nama Peserta',
                placeholder: '{{ $sampleParticipant->name ?? "Nama Peserta" }}',
                fontSize: 36,
                fontWeight: 'bold',
                color: '#f83292',
            },
            role_text: {
                id: 'role_text',
                label: 'Teks Peran',
                placeholder: 'Sebagai Peserta dalam Kegiatan',
                fontSize: 16,
                fontWeight: 'normal',
                color: '#000000',
            },
            event_name: {
                id: 'event_name',
                label: 'Nama Event',
                placeholder: '{{ $event->name }}',
                fontSize: 24,
                fontWeight: 'bold',
                color: '#005f73',
            },
            event_date: {
                id: 'event_date',
                label: 'Tanggal Event',
                placeholder: 'dilaksanakan pada {{ \Carbon\Carbon::parse($before)->translatedFormat("l, d F Y") }}',
                fontSize: 16,
                fontWeight: 'normal',
                color: '#000000',
            },
            custom_text: {
                id: 'custom_text_' + Date.now(),
                label: 'Teks Kustom',
                placeholder: 'Teks kustom Anda',
                fontSize: 16,
                fontWeight: 'normal',
                color: '#000000',
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            renderTextElements();
            initDragAndDrop();
            updateAvailableElements();

            // Click outside to deselect
            document.getElementById('certificateCanvas').addEventListener('click', function(e) {
                if (e.target === this || e.target.classList.contains('certificate-background')) {
                    deselectAll();
                }
            });
        });

        function renderTextElements() {
            const container = document.getElementById('textElementsContainer');
            container.innerHTML = '';

            textElements.forEach((element, index) => {
                const div = document.createElement('div');
                div.className = 'text-element';
                div.dataset.index = index;
                div.dataset.id = element.id;
                div.style.left = (element.x || 100) + 'px';
                div.style.top = (element.y || 100) + 'px';
                div.style.fontSize = (element.fontSize || 16) + 'px';
                div.style.fontWeight = element.fontWeight || 'normal';
                div.style.color = element.color || '#000000';
                div.style.lineHeight = '1';
                div.textContent = element.placeholder || element.label;

                div.addEventListener('mousedown', function(e) {
                    e.stopPropagation();
                    selectElement(index);
                    startDrag(e, div);
                });

                container.appendChild(div);
            });

            updateAvailableElements();
        }

        function initDragAndDrop() {
            const canvas = document.getElementById('certificateCanvas');
            const signature = document.getElementById('signatureElement');
            const resizeHandle = document.getElementById('signatureResizeHandle');

            // Signature drag
            if (signature) {
                signature.addEventListener('mousedown', function(e) {
                    // Don't drag if clicking resize handle
                    if (e.target === resizeHandle) return;
                    e.stopPropagation();
                    selectSignature();
                    startDragSignature(e, signature);
                });
            }

            // Signature resize
            if (resizeHandle) {
                resizeHandle.addEventListener('mousedown', function(e) {
                    e.stopPropagation();
                    selectSignature();
                    startResizeSignature(e);
                });
            }

            document.addEventListener('mousemove', function(e) {
                if (isDragging && selectedElement !== null) {
                    const canvas = document.getElementById('certificateCanvas');
                    const rect = canvas.getBoundingClientRect();
                    let x = (e.clientX - rect.left - dragOffset.x) / zoomLevel;
                    let y = (e.clientY - rect.top - dragOffset.y) / zoomLevel;

                    // Boundary check
                    x = Math.max(0, Math.min(x, canvas.offsetWidth - 50));
                    y = Math.max(0, Math.min(y, canvas.offsetHeight - 30));

                    const element = document.querySelector(`.text-element[data-index="${selectedElement}"]`);
                    if (element) {
                        element.style.left = x + 'px';
                        element.style.top = y + 'px';
                        element.classList.add('dragging');

                        // Update properties panel
                        document.getElementById('propX').value = Math.round(x);
                        document.getElementById('propY').value = Math.round(y);
                    }
                }
            });

            document.addEventListener('mouseup', function(e) {
                if (isDragging) {
                    isDragging = false;
                    document.querySelectorAll('.text-element').forEach(el => el.classList.remove('dragging'));

                    // Update data
                    if (selectedElement !== null) {
                        const element = document.querySelector(`.text-element[data-index="${selectedElement}"]`);
                        if (element) {
                            textElements[selectedElement].x = parseInt(element.style.left);
                            textElements[selectedElement].y = parseInt(element.style.top);
                            triggerAutoSave();
                        }
                    }
                }
            });
        }

        function startDrag(e, element) {
            isDragging = true;
            const rect = element.getBoundingClientRect();
            dragOffset.x = e.clientX - rect.left;
            dragOffset.y = e.clientY - rect.top;
        }

        let isDraggingSignature = false;
        let signatureDragOffset = {
            x: 0,
            y: 0
        };

        function startDragSignature(e, element) {
            isDraggingSignature = true;
            const rect = element.getBoundingClientRect();
            signatureDragOffset.x = e.clientX - rect.left;
            signatureDragOffset.y = e.clientY - rect.top;

            const moveHandler = function(e) {
                if (isDraggingSignature) {
                    const canvas = document.getElementById('certificateCanvas');
                    const rect = canvas.getBoundingClientRect();
                    let x = (e.clientX - rect.left - signatureDragOffset.x) / zoomLevel;
                    let y = (e.clientY - rect.top - signatureDragOffset.y) / zoomLevel;

                    x = Math.max(0, Math.min(x, canvas.offsetWidth - 100));
                    y = Math.max(0, Math.min(y, canvas.offsetHeight - 50));

                    element.style.left = x + 'px';
                    element.style.top = y + 'px';

                    // Update signature properties panel
                    document.getElementById('propSigX').value = Math.round(x);
                    document.getElementById('propSigY').value = Math.round(y);
                }
            };

            const upHandler = function(e) {
                if (isDraggingSignature) {
                    isDraggingSignature = false;
                    signaturePosition.x = parseInt(element.style.left);
                    signaturePosition.y = parseInt(element.style.top);
                    triggerAutoSave();
                }
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', upHandler);
            };

            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', upHandler);
        }

        function startResizeSignature(e) {
            const signature = document.getElementById('signatureElement');
            const startX = e.clientX;
            const startWidth = signature.offsetWidth;

            const moveHandler = function(e) {
                let newWidth = startWidth + (e.clientX - startX) / zoomLevel;
                newWidth = Math.max(50, Math.min(newWidth, 400));
                signature.style.width = newWidth + 'px';
                document.getElementById('propSigWidth').value = Math.round(newWidth);
            };

            const upHandler = function(e) {
                signaturePosition.width = parseInt(signature.style.width);
                triggerAutoSave();
                document.removeEventListener('mousemove', moveHandler);
                document.removeEventListener('mouseup', upHandler);
            };

            document.addEventListener('mousemove', moveHandler);
            document.addEventListener('mouseup', upHandler);
        }

        function selectSignature() {
            deselectAll();
            selectedSignature = true;
            const signature = document.getElementById('signatureElement');
            signature.classList.add('active');

            // Show signature properties panel
            document.getElementById('signatureProperties').classList.remove('d-none');
            document.getElementById('noElementSelected').classList.add('d-none');

            // Populate signature properties
            document.getElementById('propSigWidth').value = signaturePosition.width || 200;
            document.getElementById('propSigX').value = signaturePosition.x || 125;
            document.getElementById('propSigY').value = signaturePosition.y || 400;
        }

        function selectElement(index) {
            deselectAll();
            selectedElement = index;
            const element = document.querySelector(`.text-element[data-index="${index}"]`);
            if (element) {
                element.classList.add('active');
            }

            // Show properties panel
            document.getElementById('elementProperties').classList.remove('d-none');
            document.getElementById('noElementSelected').classList.add('d-none');

            // Populate properties
            const data = textElements[index];
            document.getElementById('propLabel').value = data.label || '';
            document.getElementById('propPlaceholder').value = data.placeholder || '';
            document.getElementById('propFontSize').value = data.fontSize || 16;
            document.getElementById('propFontWeight').value = data.fontWeight || 'normal';
            document.getElementById('propColor').value = data.color || '#000000';
            document.getElementById('propX').value = data.x || 0;
            document.getElementById('propY').value = data.y || 0;
        }

        function deselectAll() {
            selectedElement = null;
            selectedSignature = false;
            document.querySelectorAll('.text-element').forEach(el => el.classList.remove('active'));
            document.getElementById('signatureElement')?.classList.remove('active');
            document.getElementById('elementProperties').classList.add('d-none');
            document.getElementById('signatureProperties').classList.add('d-none');
            document.getElementById('noElementSelected').classList.remove('d-none');
        }

        // Property change handlers
        document.getElementById('propPlaceholder')?.addEventListener('input', function() {
            if (selectedElement !== null) {
                textElements[selectedElement].placeholder = this.value;
                renderTextElements();
                selectElement(selectedElement);
                triggerAutoSave();
            }
        });

        document.getElementById('propFontSize')?.addEventListener('input', function() {
            if (selectedElement !== null) {
                textElements[selectedElement].fontSize = parseInt(this.value);
                renderTextElements();
                selectElement(selectedElement);
                triggerAutoSave();
            }
        });

        document.getElementById('propFontWeight')?.addEventListener('change', function() {
            if (selectedElement !== null) {
                textElements[selectedElement].fontWeight = this.value;
                renderTextElements();
                selectElement(selectedElement);
                triggerAutoSave();
            }
        });

        document.getElementById('propColor')?.addEventListener('input', function() {
            if (selectedElement !== null) {
                textElements[selectedElement].color = this.value;
                renderTextElements();
                selectElement(selectedElement);
                triggerAutoSave();
            }
        });

        document.getElementById('propX')?.addEventListener('input', function() {
            if (selectedElement !== null) {
                textElements[selectedElement].x = parseInt(this.value);
                renderTextElements();
                selectElement(selectedElement);
                triggerAutoSave();
            }
        });

        document.getElementById('propY')?.addEventListener('input', function() {
            if (selectedElement !== null) {
                textElements[selectedElement].y = parseInt(this.value);
                renderTextElements();
                selectElement(selectedElement);
                triggerAutoSave();
            }
        });

        // Signature property change handlers
        document.getElementById('propSigWidth')?.addEventListener('input', function() {
            if (selectedSignature) {
                signaturePosition.width = parseInt(this.value);
                document.getElementById('signatureElement').style.width = this.value + 'px';
                triggerAutoSave();
            }
        });

        document.getElementById('propSigX')?.addEventListener('input', function() {
            if (selectedSignature) {
                signaturePosition.x = parseInt(this.value);
                document.getElementById('signatureElement').style.left = this.value + 'px';
                triggerAutoSave();
            }
        });

        document.getElementById('propSigY')?.addEventListener('input', function() {
            if (selectedSignature) {
                signaturePosition.y = parseInt(this.value);
                document.getElementById('signatureElement').style.top = this.value + 'px';
                triggerAutoSave();
            }
        });

        function addElement(elementId) {
            const config = JSON.parse(JSON.stringify(defaultElements[elementId] || defaultElements.custom_text));
            if (elementId === 'custom_text') {
                config.id = 'custom_text_' + Date.now();
            }
            config.x = 100 + (textElements.length * 20);
            config.y = 100 + (textElements.length * 30);
            textElements.push(config);
            renderTextElements();
            selectElement(textElements.length - 1);
            triggerAutoSave();
        }

        function removeSelectedElement() {
            if (selectedElement !== null) {
                textElements.splice(selectedElement, 1);
                deselectAll();
                renderTextElements();
                triggerAutoSave();
            }
        }

        function updateAvailableElements() {
            const buttons = document.querySelectorAll('.available-element-btn');
            buttons.forEach(btn => {
                const elementId = btn.dataset.element;
                const exists = textElements.some(el => el.id === elementId);
                if (exists && elementId !== 'custom_text') {
                    btn.classList.add('added');
                } else {
                    btn.classList.remove('added');
                }
            });

            // Add click handlers
            buttons.forEach(btn => {
                btn.onclick = function() {
                    addElement(btn.dataset.element);
                };
            });
        }

        function zoomIn() {
            if (zoomLevel < 1.5) {
                zoomLevel += 0.1;
                applyZoom();
            }
        }

        function zoomOut() {
            if (zoomLevel > 0.5) {
                zoomLevel -= 0.1;
                applyZoom();
            }
        }

        function applyZoom() {
            const canvas = document.getElementById('certificateCanvas');
            canvas.style.transform = `scale(${zoomLevel})`;
            canvas.style.transformOrigin = 'top left';
            document.getElementById('zoomLevel').textContent = Math.round(zoomLevel * 100) + '%';
        }

        function triggerAutoSave() {
            clearTimeout(autoSaveTimeout);
            showSaveIndicator('saving');
            autoSaveTimeout = setTimeout(savePositions, 1000);
        }

        function savePositions() {
            fetch('{{ route('back.event.detail.certificate.save-positions', [$event->id, $template->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        text_elements: textElements,
                        signature_position: signaturePosition
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        showSaveIndicator('saved');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showSaveIndicator('saved'); // Still mark as saved to avoid spam
                });
        }

        function saveTemplate() {
            const formData = new FormData(document.getElementById('templateSettingsForm'));
            formData.append('text_elements', JSON.stringify(textElements));
            formData.append('signature_position', JSON.stringify(signaturePosition));
            formData.append('_method', 'PUT');

            fetch('{{ route('back.event.detail.certificate.update', [$event->id, $template->id]) }}', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        return response.text().then(text => {
                            // Check if response is HTML (error page)
                            if (text.startsWith('<!DOCTYPE') || text.startsWith('<html')) {
                                throw new Error('Server error. Silakan cek log untuk detail.');
                            }
                            try {
                                const data = JSON.parse(text);
                                throw new Error(data.message || 'Terjadi kesalahan');
                            } catch (e) {
                                throw new Error('Terjadi kesalahan pada server');
                            }
                        });
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Berhasil',
                            text: 'Template berhasil disimpan!',
                            timer: 2000,
                            showConfirmButton: false
                        });

                        // Reload if template image changed
                        if (formData.get('template_image')?.size > 0) {
                            location.reload();
                        }
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message || 'Terjadi kesalahan saat menyimpan template'
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: error.message || 'Terjadi kesalahan saat menyimpan template'
                    });
                });
        }

        function showSaveIndicator(state) {
            const indicator = document.getElementById('saveIndicator');
            const text = document.getElementById('saveIndicatorText');

            indicator.classList.remove('hidden', 'saving', 'saved');

            if (state === 'saving') {
                indicator.classList.add('saving');
                text.textContent = 'Menyimpan...';
            } else if (state === 'saved') {
                indicator.classList.add('saved');
                text.textContent = 'Tersimpan';
                setTimeout(() => {
                    indicator.classList.add('hidden');
                }, 2000);
            }
        }

        function previewNewTemplate(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('templateBackground').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function previewNewSignature(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('signatureImg').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function confirmDelete() {
            Swal.fire({
                title: 'Hapus Template?',
                text: "Template sertifikat akan dihapus. Anda dapat membuat template baru setelahnya.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-template-form').submit();
                }
            });
        }
    </script>

    <form id="delete-template-form" action="{{ route('back.event.detail.certificate.destroy', [$event->id, $template->id]) }}" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>
@endsection
