<?= $this->extend('julio101290\boilerplate\Views\Authentication\index') ?>
<?= $this->section('content') ?>

<style>
    body.login-page {
        background: 
        <?php if (file_exists(FCPATH . 'img/back.png')): ?>
            /* Si la imagen existe en public/img/back.png, la aplica encima del degradado */
            url('<?= base_url('img/back.png') ?>') no-repeat center center fixed,
        <?php endif; ?>
            /* Degradados por defecto si no existe la imagen o como respaldo */
            radial-gradient(circle at top left, #1e3c72, transparent 60%),
            radial-gradient(circle at bottom right, #2a5298, transparent 60%),
            linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        background-size: cover;
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

    .card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(8px);
        border-radius: 14px;
        box-shadow: 0 25px 45px rgba(0,0,0,.25);
        border: none;
    }

    .register-card-body {
        padding: 2rem;
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
</style>

<div class="card">
  <div class="card-body register-card-body">
    <p class="login-box-msg"><?=lang('Auth.register')?></p>
    <?= $this->include('julio101290\boilerplate\Views\Authentication\message_block') ?>
    <form action="<?= base_url(route_to('register')) ?>" method="post">
      <?= csrf_field() ?>
      <div class="input-group mb-3">
        <input type="text" name="username"
          class="form-control <?= session('errors.username') ? 'is-invalid' : '' ?>"
          placeholder="<?=lang('Auth.username')?>" value="<?= old('username') ?>">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-user"></span>
          </div>
        </div>
        <div class="invalid-feedback">
          <?= session('errors.username') ?>
        </div>
      </div>
      <div class="input-group mb-3">
        <input type="email" name="email"
          class="form-control <?= session('errors.email') ? 'is-invalid' : '' ?>"
          placeholder="<?=lang('Auth.email')?>" value="<?= old('email') ?>">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-envelope"></span>
          </div>
        </div>
        <div class="invalid-feedback">
          <?= session('errors.email') ?>
        </div>
      </div>
      <div class="input-group mb-3">
        <input type="password" name="password"
          class="form-control <?= session('errors.password') ? 'is-invalid' : '' ?>"
          placeholder="<?=lang('Auth.password')?>" autocomplete="off">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
        <div class="invalid-feedback">
          <?= session('errors.password') ?>
        </div>
      </div>
      <div class="input-group mb-3">
        <input type="password" name="pass_confirm"
          class="form-control <?= session('errors.pass_confirm') ? 'is-invalid' : '' ?>"
          placeholder="<?=lang('Auth.repeatPassword')?>" autocomplete="off">
        <div class="input-group-append">
          <div class="input-group-text">
            <span class="fas fa-lock"></span>
          </div>
        </div>
        <div class="invalid-feedback">
          <?= session('errors.pass_confirm') ?>
        </div>
      </div>
      <div class="row">
        <!-- /.col -->
        <div class="col-12">
          <button type="submit" class="btn btn-primary btn-block"><?=lang('Auth.register')?></button>
        </div>
        <!-- /.col -->
      </div>
    </form>

    <p class="mt-3 mb-0"><?=lang('Auth.alreadyRegistered')?> <a href="<?= base_url(route_to('login')) ?>"
        class="text-center"><?=lang('Auth.signIn')?></a></p>
  </div>
  <!-- /.form-box -->
</div><!-- /.card -->
<?= $this->endSection() ?>