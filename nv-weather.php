<?php
// Configuración de OpenWeatherMap
$apiKey = 'tu_clave_api_aqui';
$city = $argv[1]; // La ciudad se pasa como argumento desde Asterisk
$apiUrl = "http://api.openweathermap.org/data/2.5/weather?q={$city},ES&appid={$apiKey}&units=metric&lang=es";

// Realizar la solicitud a la API
$response = file_get_contents($apiUrl);
$data = json_decode($response, true);

// Verificar si la solicitud fue exitosa
if ($data['cod'] == 200) {
    $weatherDescription = $data['weather'][0]['description'];
    $temperature = $data['main']['temp'];
    $humidity = $data['main']['humidity'];
    $windSpeed = $data['wind']['speed'];

    // Generar el mensaje de salida
    $outputMessage = "El clima en {$city} es {$weatherDescription} con una temperatura de {$temperature} grados Celsius, humedad del {$humidity}% y viento a {$windSpeed} km/h.";

    // Crear el archivo de audio con el mensaje
    $textToSpeech = new TextToSpeech();
    $audioFile = $textToSpeech->textToSpeech($outputMessage);

    // Establecer la variable TMPWAVE para que Asterisk pueda reproducir el archivo de audio
    echo "TMPWAVE=$audioFile\n";
} else {
    // Manejar errores
    echo "TMPWAVE=error.wav\n";
}

class TextToSpeech {
    public function textToSpeech($text) {
        // Ruta al ejecutable de Flite
        $flitePath = '/usr/bin/flite'; // Ajusta esta ruta según tu instalación

        // Ruta al archivo de salida
        $outputFile = '/ruta/al/archivo/audio.wav'; // Ajusta esta ruta según tus necesidades

        // Comando para ejecutar Flite
        $command = escapeshellcmd("$flitePath -t \"$text\" -o \"$outputFile\"");

        // Ejecutar el comando
        shell_exec($command);

        // Devolver la ruta del archivo de audio generado
        return $outputFile;
    }
}
?>
