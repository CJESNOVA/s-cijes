@extends('layouts.app')

@section('content')
<!-- Header -->
<header class="header" data-testid="header">
    <div class="header-content">
        <div class="header-left">
            <h1 class="page-title">Créer un nouveau ticket</h1>
            <p class="page-subtitle">Décrivez votre problème ou votre demande</p>
        </div>
    </div>
</header>

<!-- Create Ticket Form -->
<div class="create-ticket-container" data-testid="create-ticket-form">
    <div class="form-card">
        <form action="{{ route('tickets.store') }}" method="POST" enctype="multipart/form-data" class="ticket-form">
            @csrf

            <!-- Plateforme et Module -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="plateforme_id" data-testid="label-platform">Plateforme *</label>
                    <select name="plateforme_id" id="plateforme_id" class="form-select" required data-testid="select-platform">
                        <option value="">Sélectionnez une plateforme</option>
                        @foreach($plateformes as $plateforme)
                            <option value="{{ $plateforme->id }}">{{ $plateforme->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="module_id" data-testid="label-module">Module *</label>
                    <select name="module_id" id="module_id" class="form-select" disabled required data-testid="select-module">
                        <option value="">-- Sélectionnez d'abord une plateforme --</option>
                    </select>
                </div>
            </div>

            <!-- Catégorie et Priorité -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="form-group">
                    <label class="form-label" for="categorie_id" data-testid="label-category">Catégorie *</label>
                    <select name="categorie_id" id="ticket-category" class="form-select" required data-testid="select-category">
                        <option value="">Sélectionnez une catégorie</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nom }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label" for="priorite_id" data-testid="label-priority">Priorité</label>
                    <select name="priorite_id" id="ticket-priority" class="form-select" data-testid="select-priority">
                        @foreach($priorites as $prio)
                            <option value="{{ $prio->id }}" {{ $prio->niveau == 2 ? 'selected' : '' }}>{{ $prio->nom }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Titre -->
            <div class="form-group">
                <label class="form-label" for="ticket-title" data-testid="label-title">Titre du ticket *</label>
                <input 
                    type="text" 
                    id="ticket-title" 
                    name="titre"
                    class="form-input" 
                    placeholder="Bref résumé de votre problème" 
                    required
                    data-testid="input-title"
                >
            </div>

            <!-- Description -->
            <div class="form-group">
                <label class="form-label" for="ticket-description" data-testid="label-description">Description *</label>
                <textarea 
                    id="ticket-description" 
                    name="description"
                    class="form-textarea" 
                    rows="8" 
                    placeholder="Décrivez votre problème en détail..." 
                    required
                    data-testid="textarea-description"
                ></textarea>
                <p class="form-help-text">Soyez aussi précis que possible pour nous aider à résoudre votre problème rapidement.</p>
            </div>

            <!-- Pièces jointes -->
            <div class="form-group">
                <label class="form-label" data-testid="label-attachments">Pièces jointes</label>
                <div class="dropzone" data-testid="dropzone">
                    <svg class="dropzone-icon" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    <p class="dropzone-text">
                        <span class="dropzone-text-bold">Cliquez pour sélectionner</span> ou glissez-déposez vos fichiers ici
                    </p>
                    <p class="dropzone-hint">PNG, JPG, PDF jusqu'à 5MB</p>
                    <input type="file" id="file-input" name="attachments[]" class="file-input" multiple data-testid="file-input">
                </div>
                <div class="uploaded-files" id="uploaded-files" data-testid="uploaded-files">
                    <!-- Files will be displayed here -->
                </div>
                <div id="file-list" class="mt-2 text-sm text-gray-500"></div>
            </div>

            <!-- Actions -->
            <div class="form-actions">
                <a href="{{ route('dashboard') }}" class="btn btn-secondary" data-testid="cancel-button">Annuler</a>
                <button type="submit" class="btn btn-primary" data-testid="submit-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 12h14"/>
                        <path d="m12 5 7 7-7 7"/>
                    </svg>
                    Créer le ticket
                </button>
            </div>
        </form>
    </div>

    <!-- Help Card -->
    <div class="help-card" data-testid="help-card">
        <h3 class="help-card-title">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Conseils pour un ticket efficace
        </h3>
        <ul class="help-list">
            <li class="help-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Utilisez un titre clair et descriptif</span>
            </li>
            <li class="help-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Incluez les étapes pour reproduire le problème</span>
            </li>
            <li class="help-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Ajoutez des captures d'écran si pertinent</span>
            </li>
            <li class="help-item">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                <span>Précisez votre navigateur et système d'exploitation</span>
            </li>
        </ul>

        <div class="help-contact">
            <p class="help-contact-text">Besoin d'aide urgente ?</p>
            <a href="#" class="help-contact-link" data-testid="urgent-help-link">
                Contactez-nous directement
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M5 12h14"/>
                    <path d="m12 5 7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Filtrage dynamique des modules
    document.getElementById('plateforme_id').addEventListener('change', function() {
        const plateformeId = this.value;
        const moduleSelect = document.getElementById('module_id');
        
        moduleSelect.innerHTML = '<option value="">Chargement...</option>';
        moduleSelect.disabled = true;

        if (plateformeId) {
            fetch(`/api/plateformes/${plateformeId}/modules`)
                .then(res => res.json())
                .then(data => {
                    moduleSelect.innerHTML = '<option value="">-- Choisir un module --</option>';
                    data.forEach(module => {
                        moduleSelect.innerHTML += `<option value="${module.id}">${module.nom}</option>`;
                    });
                    moduleSelect.disabled = false;
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    moduleSelect.innerHTML = '<option value="">Erreur de chargement</option>';
                });
        } else {
            moduleSelect.innerHTML = '<option value="">-- Sélectionnez d\'abord une plateforme --</option>';
        }
    });

    // Dropzone functionality
    const dropzone = document.querySelector('.dropzone');
    const fileInput = document.getElementById('file-input');
    const uploadedFiles = document.getElementById('uploaded-files');
    let uploadedFilesList = [];

    dropzone.addEventListener('click', () => fileInput.click());

    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '#2563EB';
        dropzone.style.background = '#EFF6FF';
    });

    dropzone.addEventListener('dragleave', () => {
        dropzone.style.borderColor = '';
        dropzone.style.background = '';
    });

    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.style.borderColor = '';
        dropzone.style.background = '';
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });

    function handleFiles(files) {
        Array.from(files).forEach(file => {
            if (file.size > 5 * 1024 * 1024) {
                alert('Le fichier ' + file.name + ' dépasse la taille maximale de 5MB');
                return;
            }

            if (!file.type.match(/image\/(jpeg|jpg|png)|application\/pdf/)) {
                alert('Le fichier ' + file.name + ' n\'est pas un format supporté (PNG, JPG, PDF)');
                return;
            }

            uploadedFilesList.push(file);
            displayUploadedFile(file);
        });
    }

    function displayUploadedFile(file) {
        const fileElement = document.createElement('div');
        fileElement.className = 'uploaded-file';
        fileElement.innerHTML = `
            <span>${file.name}</span>
            <button type="button" onclick="removeFile('${file.name}')">×</button>
        `;
        uploadedFiles.appendChild(fileElement);
    }

    function removeFile(fileName) {
        uploadedFilesList = uploadedFilesList.filter(file => file.name !== fileName);
        const fileElements = uploadedFiles.querySelectorAll('.uploaded-file');
        fileElements.forEach(el => {
            if (el.textContent.includes(fileName)) {
                el.remove();
            }
        });
    }

    // Afficher les noms des fichiers sélectionnés
    fileInput.addEventListener('change', function(e) {
        const fileList = document.getElementById('file-list');
        fileList.innerHTML = '';
        for (let i = 0; i < e.target.files.length; i++) {
            fileList.innerHTML += `<div>(${e.target.files[i].size > 1024*1024 ? (e.target.files[i].size/1024/1024).toFixed(2)+'MB' : (e.target.files[i].size/1024).toFixed(2)+'KB'}) ${e.target.files[i].name}</div>`;
        }
    });
</script>
@endpush
@endsection
