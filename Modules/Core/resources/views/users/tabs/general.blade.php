<form id="generalForm" action="{{ route('cores.users.update', $user->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row mb-3">
        <div class="col-md-12 text-center">
             <div class="position-relative d-inline-block">
                <img id="edit-avatar-preview" src="{{ $user->avatar_url }}" 
                     class="rounded-circle img-thumbnail" style="width: 150px; height: 150px; object-fit: cover;" alt="Avatar">
                <label for="edit-avatar" class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle" style="cursor: pointer;">
                    <i class="fas fa-camera"></i>
                </label>
                <input type="file" id="edit-avatar" name="avatar" class="d-none" accept="image/*">
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="name">Prénom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
            </div>
        </div>
        <div class="col-md-6">
             <div class="form-group">
                <label for="last_name">Nom <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $user->last_name }}" required>
            </div>
        </div>
    </div>

    <div class="row m-t-small">
        <div class="col-md-6">
            <div class="form-group">
                <label for="user_name">Nom d'utilisateur <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="user_name" name="user_name" value="{{ $user->user_name }}" required>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="email">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
            </div>
        </div>
    </div>

    <div class="form-group m-t-small">
        <label for="service">Service</label>
        <input type="text" class="form-control" id="service" name="service" value="{{ $user->service }}">
    </div>

    <div class="password-group mt-4">
        <h5 class="text-primary"><i class="fas fa-lock"></i> Sécurité</h5>
        <hr>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password">Nouveau mot de passe</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" minlength="8" placeholder="Laisser vide pour conserver l'actuel">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="password_confirmation">Confirmer le mot de passe</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Répéter le mot de passe">
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#password_confirmation">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12 text-end">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Enregistrer les modifications
            </button>
        </div>
    </div>
</form>

<script>
document.getElementById('edit-avatar').addEventListener('change', function(event) {
    const file = event.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('edit-avatar-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = document.querySelector(this.getAttribute('data-target'));
        const icon = this.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
});
</script>
