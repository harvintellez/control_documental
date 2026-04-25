
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acceso al Sistema - Control Documental de NSEL-CLNSA</title>
<link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/isa-colors.css" rel="stylesheet">
    <link rel="stylesheet" href="font/bootstrap-icons.css"> 
    <style>
        body {
            background: linear-gradient(135deg, #1d9140 0%, #0d5a2a 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5 col-lg-4">
            <div class="card login-card p-4">
                <div class="text-center mb-4">
                    <img src="img/isa-logo.png" alt="ISA Logo" style="max-height: 60px;" class="mb-3">
                    <h3 class="fw-bold">Bienvenido</h3>
                    <p class="text-muted">Inicia sesión para gestionar documentos</p>
                </div>
                
                <form action="validar_login.php" method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Usuario</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-person"></i></span>
                            <input type="text" name="usuario" class="form-control" placeholder="Nombre de usuario" required>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Contraseña</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-key"></i></span>
                            <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg fw-bold">Entrar al Sistema</button>
                    </div>
                </form>
            </div>
            <p class="text-center mt-4 text-white-50 small">&copy; 2026 Sistema de Control Documental</p>
        </div>
    </div>
</div>

<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>