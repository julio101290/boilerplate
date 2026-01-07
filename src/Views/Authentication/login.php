<?= $this->extend('julio101290\boilerplate\Views\Authentication\index') ?>
<?= $this->section('content') ?>

<style>
    body.login-page {
        background:
            radial-gradient(circle at top left, #1e3c72, transparent 60%),
            radial-gradient(circle at bottom right, #2a5298, transparent 60%),
            linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        min-height: 100vh;
    }

    .login-box {
        animation: fadeSlide .6s ease-out;
    }

    @keyframes fadeSlide {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .login-logo {
        font-size: 1.8rem;
        color: #fff;
        text-align: center;
        margin-bottom: 1.2rem;
        text-shadow: 0 4px 10px rgba(0,0,0,.35);
    }

    .card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border-radius: 14px;
        box-shadow: 0 25px 45px rgba(0,0,0,.25);
        border: none;
    }

    .login-card-body {
        padding: 2rem;
    }

    .input-group-text {
        background: transparent;
        border-left: none;
        cursor: pointer;
    }

    .form-control {
        border-right: none;
    }

    .form-control:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 .15rem rgba(74,144,226,.25);
    }

    .btn-primary {
        border-radius: 25px;
        font-weight: 600;
        letter-spacing: .4px;
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 14px rgba(0,0,0,.25);
    }

    @media (max-width: 576px) {
        .login-card-body {
            padding: 1.5rem;
        }
    }
</style>

<div class="login-box">

    <!-- Título configurable -->
    <div class="login-logo">
        <?= lang('Auth.loginTitle') ?>
    </div>

    <div class="card">
        <div class="card-body login-card-body">

            <?= $this->include('julio101290\boilerplate\Views\Authentication\message_block') ?>

            <form action="<?= base_url(route_to('login')) ?>" method="post">
                <?= csrf_field() ?>

                <!-- LOGIN -->
                <div class="input-group mb-4">
                    <input
                        type="<?= $config->validFields === ['email'] ? 'email' : 'text' ?>"
                        name="login"
                        class="form-control <?= session('errors.login') ? 'is-invalid' : '' ?>"
                        placeholder="<?= $config->validFields === ['email']
                            ? lang('Auth.email')
                            : lang('Auth.emailOrUsername') ?>"
                        value="<?= old('login') ?>"
                        autocomplete="off">
                    <div class="input-group-append">
                        <div class="input-group-text">
                            <span class="fas fa-user"></span>
                        </div>
                    </div>
                    <div class="invalid-feedback">
                        <?= session('errors.login') ?>
                    </div>
                </div>

                <!-- PASSWORD -->
                <div class="input-group mb-3">
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
                        placeholder="<?= lang('Auth.password') ?>">
                    <div class="input-group-append">
                        <div class="input-group-text" onclick="togglePassword()">
                            <span id="toggleIcon" class="fas fa-eye"></span>
                        </div>
                    </div>
                    <div class="invalid-feedback">
                        <?= session('errors.password') ?>
                    </div>
                </div>

                <!-- SHOW PASSWORD -->
                <div class="form-group mb-3 text-right">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox"
                               class="custom-control-input"
                               id="showPassword"
                               onclick="togglePassword()">
                        <label class="custom-control-label" for="showPassword">
                            <?= lang('boilerplate.Auth.showPassword') ?? 'Ver contraseña' ?>
                        </label>
                    </div>
                </div>

                <div class="row mb-3">
                    <?php if ($config->allowRemembering) { ?>
                        <div class="col-8">
                            <div class="icheck-primary">
                                <input type="checkbox"
                                       id="remember"
                                       name="remember"
                                       <?= old('remember') ? 'checked' : '' ?>>
                                <label for="remember">
                                    <?= lang('Auth.rememberMe') ?>
                                </label>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-4">
                        <button type="submit" class="btn btn-primary btn-block">
                            <?= lang('Auth.signIn') ?>
                        </button>
                    </div>
                </div>
            </form>

            <hr>

            <p class="mb-1 text-center">
                <a href="<?= route_to('forgot') ?>">
                    <?= lang('Auth.forgotYourPassword') ?>
                </a>
            </p>

            <?php if ($config->allowRegistration) { ?>
                <p class="mb-0 text-center">
                    <a href="<?= route_to('register') ?>">
                        <?= lang('Auth.needAnAccount') ?>
                    </a>
                </p>
            <?php } ?>

        </div>
    </div>
</div>

<script>
function togglePassword() {
    const input = document.getElementById('password');
    const icon  = document.getElementById('toggleIcon');
    const check = document.getElementById('showPassword');

    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
        if (check) check.checked = true;
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
        if (check) check.checked = false;
    }
}
</script>

<?= $this->endSection() ?>
