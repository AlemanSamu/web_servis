<?php
class Robon {
    public $nombre;
    public $colorPrincipal;
    public $altura;
    public $energia;

    public function __construct($nombre, $colorPrincipal, $altura, $energia) {
        $this->nombre = $nombre;
        $this->colorPrincipal = $colorPrincipal;
        $this->altura = $altura;
        $this->energia = $energia;
    }

    public function mostrarInfo() {
        return "
        <strong>🤖 Robón:</strong> {$this->nombre}<br>
        <strong>🎨 Colores:</strong> {$this->colorPrincipal}<br>
        <strong>📏 Altura:</strong> {$this->altura} metros<br>
        <strong>⚡ Energía:</strong> {$this->energia}%<br>
        ";
    }

    public function activar() {
        return "{$this->nombre} se ha activado con energía al {$this->energia}%.";
    }

    public function atacar() {
        if ($this->energia >= 20) {
            $this->energia -= 20;
            return "{$this->nombre} ha lanzado un rayo de color {$this->colorPrincipal}. Energía restante: {$this->energia}% ⚡";
        } else {
            return "{$this->nombre} no tiene suficiente energía para atacar.";
        }
    }

    public function recargarEnergia() {
        $this->energia = 100;
        return "{$this->nombre} ha recargado su energía al 100%. 🔋";
    }
}

$robon1 = new Robon("TitaniumX", "rojo, plata, verde aguamarina y dorado", 2.5, 80);
$resultado = $robon1->mostrarInfo();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Robón Aesthetic</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #ffe3e3, #c1f8cf, #e0f7fa);
            background-size: 300% 300%;
            animation: gradient 10s ease infinite;
            font-family: 'Quicksand', sans-serif;
            color: #333;
        }

        @keyframes gradient {
            0% {background-position: 0% 50%;}
            50% {background-position: 100% 50%;}
            100% {background-position: 0% 50%;}
        }

        .card-robon {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            color: #222;
        }

        .card-title {
            color: #ff4d6d;
            font-weight: 600;
        }

        .btn-aesthetic {
            border-radius: 30px;
            padding: 10px 20px;
            font-weight: bold;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-aesthetic:hover {
            transform: scale(1.05);
            opacity: 0.9;
        }

        .btn-danger {
            background-color: #ff4d6d;
        }

        .btn-warning {
            background-color: #ffc107;
        }

        .btn-success {
            background-color: #4caf50;
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card card-robon p-4 w-100" style="max-width: 500px;">
        <div class="card-body text-center">
            <h2 class="card-title">✨ Perfil del Robón ✨</h2>
            <p class="card-text"><?php echo $resultado; ?></p>
           
        </div>
    </div>
</div>

</body>
</html>
