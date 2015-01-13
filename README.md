# HiperBox

Aplicación Web basada en el concepto de Single Page Application (SPA) destinada a recomendar y buscar artistas nuevos al usuario. El usuario podrá escuchar una canción del artista, y si lo desea, se facilita la página de Spotify del artista para poder escuchar más canciones. También se le permite al usuario guardar el artista deseado en una playlist.

##  Funcionalidades

* **Buscador:** La aplicación tiene un pequeño buscador que permitirá al usuario buscar artistas.
* **Recomendaciones:** La aplicación recomendará otros artistas a partir del artistas que estás escuchando.
* **Playlist:** La aplicación permite guardar los artistas que el usuario quiera en una base de datos.
* **Reproductor YouTube:** La aplicación utiliza el reproductor de Youtube para poder escuchar al artista seleccionado.
* **Ir a Spotify:** En el caso que el artista sea del agrado del usuario y se desee escuchar más canciones, la aplicació proporciona un link para poder ir a escucharlo a la página del artista en Spotify.

## APIs utilizadas
* **the Echo Nest:** API utilizada para buscar y recomendar artistas. http://developer.echonest.com/
* **Spotify:** API utilizada para obtener el enlace del artista y así poder acceder a su página en Spotify. https://developer.spotify.com/
* **YouTube:** No se utiliza la API en sí de YouTube, solo se utiliza el embed de YouTube. https://www.youtube.com/
* **Hipermedia:** API facilitada por la asignatura de Programación Hipermedia para poder acceder a la base de datos. https://github.com/raulmoron/vmhipermedia