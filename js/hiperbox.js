
var Song = {
	id: "0",
	name: "0",
	url: "0",
	spoty_id: "0"
}

var Echonest = {
	api_key: 'FC7JZ7SEZQQ9HSPNX',
	results: '4',
}

var Data = {
	busqueda: "0",
	busLength: "0",
	recomendaciones: "0",
	recLength: "0",
	playlist: "0",
	plaLength: "0",
}

var AJAX = {
	requestGET: function(url){
		var xhr = new XMLHttpRequest();
		xhr.open("GET", url, false);
		xhr.send();
		return xhr.responseText;
	},
	requestPUT: function(url,send){
		var xhr = new XMLHttpRequest();
		xhr.open("PUT", url, false);
		xhr.send(send);
		return xhr.responseText;
	},
	requestPOST: function(url,send){
		var xhr = new XMLHttpRequest();
		xhr.open("POST", url);
		xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
		xhr.send(send);
		return xhr.responseText;
	}
}

var DB = {
	addSong: function(){
		var url = "http://api.hipermedia.local/track/id/"+Song.id;
		var json_response = AJAX.requestGET(url);
		var response = JSON.parse(json_response);
		console.log(response);
		console.log(Song.id);
		if(response.data.length == 0){
			url = "http://api.hipermedia.local/music/new";
			var send = "artist="+Song.name+"&title="+Song.url+"&track_id="+Song.id+"&playlist_id=2";
			var result = AJAX.requestPOST(url,send);
		}else{
			console.log('Artist repeated.');
		}
	},
	getLastSongs: function(){
		var url = "http://api.hipermedia.local/music";
		var json_response = AJAX.requestGET(url);
		response = JSON.parse(json_response);
		return response;
	},
	getSong: function(musicID){
		if(musicID > -1){
			var url = "http://api.hipermedia.local/music/"+musicID;
			var json_response = AJAX.requestGET(url);
			var response = JSON.parse(json_response);
		}else{
			response = -1;
		}
		return response;
	},
	getRandomSong: function(){
		var lastSongs = DB.getLastSongs();
		if(lastSongs.data.length > 0){
			var random = Math.floor(Math.random() * lastSongs.data.length);
			var id = lastSongs.data[random].music_id;
		}else{
			id = -1;
		}
		return id;
	},
	getPlaylist: function(){
		var url = "http://api.hipermedia.local/query";
		var send = "SELECT * FROM playlists INNER JOIN music ON playlists.playlist_id=music.playlist_id WHERE playlists.playlist_id=2";
		var json_response = AJAX.requestPUT(url, send);
		response = JSON.parse(json_response);
		return response;
	},
	deleteArtist: function(idArtist){
		var url = "http://api.hipermedia.local/query";
		var send = "DELETE FROM music WHERE music_id="+idArtist;
		var json_response = AJAX.requestPUT(url, send);
		response = JSON.parse(json_response);
		console.log(response);
		return response;
	},
}

var Listener = {
	add: function(object,event,callback,capture){
		object.addEventListener(event,callback,capture);
	},
	eventPlayRec: function(event){
		console.log("Play: "+this.title);
		Layout.renderYoutube(Data.recomendaciones.artists[this.alt]);
		Search.searchRecomendados(Data.recomendaciones.artists[this.alt].id);
		Layout.renderRecomendadosContainer();
	},
	eventPlayBus: function(event){
		console.log("Play: "+this.title);
		Layout.renderYoutube(Data.busqueda.artists[this.alt]);
		Search.searchRecomendados(Data.busqueda.artists[this.alt].id);
		Layout.renderRecomendadosContainer();
	},
	eventPlayPla: function(event){
		console.log("Play: "+this.title);
		var artist = Search.searchArtist(Data.playlist.response[this.alt].track_id);
		Layout.renderYoutube(artist.response.artist);
		Search.searchRecomendados(Data.playlist.response[this.alt].track_id);
		Layout.renderRecomendadosContainer();
	},
	eventSearch: function(event){
		var api_url = "http://developer.echonest.com/api/v4/artist/search?api_key="+Echonest.api_key+"&format=json&name="+this.parentNode.firstChild.nextSibling.value+"&results=10&bucket=biographies&bucket=images&bucket=video&bucket=id:spotify";
		var json_response = AJAX.requestGET(api_url);
		var response = JSON.parse(json_response);
		Data.busqueda = response.response;
		Data.busLength = response.response.artists.length;
		Layout.renderSearchContainer();
	},
	eventKeySearch: function(event){
		if(event.keyCode == 13){
			var api_url = "http://developer.echonest.com/api/v4/artist/search?api_key="+Echonest.api_key+"&format=json&name="+this.parentNode.firstChild.nextSibling.value+"&results=10&bucket=biographies&bucket=images&bucket=video&bucket=id:spotify";
			var json_response = AJAX.requestGET(api_url);
			var response = JSON.parse(json_response);
			Data.busqueda = response.response;
			Data.busLength = response.response.artists.length;
			Layout.renderSearchContainer();
		}
	},
	eventAddToPlaylist: function(event){
		DB.addSong();
		Playlist.loadPlaylist();
	},
	eventPlaylistUp: function(event){
		if(Playlist.position > 0){
			Playlist.position = Playlist.position - 1;
		}
		Playlist.loadPlaylist();
	},
	eventPlaylistDown: function(event){
		if(Playlist.position < (Data.plaLength-4)){
			Playlist.position = Playlist.position + 1;
		}
		Playlist.loadPlaylist();		
	},
	eventGoToSpotify: function(event){
		var url = "https://api.spotify.com/v1/artists/"+Song.spoty_id;
		var json_response = AJAX.requestGET(url);
		var response = JSON.parse(json_response);
		window.open(response.external_urls.spotify);
	},
	eventDeleteArtist: function(event){
		var aux = this.id;
		aux = aux.replace('delete', '');
		var id = parseInt(aux);
		var pos = Playlist.position + id - 1;
		console.log(Data.playlist.response[pos].music_id);
		DB.deleteArtist(Data.playlist.response[pos].music_id);
		Playlist.loadPlaylist();
	},
}

var Search = {
	searchArtist: function(idArtista){
		var api_url = "http://developer.echonest.com/api/v4/artist/profile?api_key="+Echonest.api_key+"&id="+idArtista+"&bucket=biographies&bucket=images&bucket=video&bucket=id:spotify";
		var json_response = AJAX.requestGET(api_url);
		var response = JSON.parse(json_response);
		console.log(response);
		return response;
	},
	searchRecomendados: function(idArtista){
		var api_url = "http://developer.echonest.com/api/v4/artist/similar?api_key="+Echonest.api_key+"&id="+idArtista+"&results=10&bucket=biographies&bucket=images&bucket=video&bucket=id:spotify";
		var json_response = AJAX.requestGET(api_url);
		var response = JSON.parse(json_response);
		Data.recomendaciones = response.response;
		Data.recLength = response.response.artists.length;
		return response;
	},
	searchTopArtists: function(){
		var url = "http://developer.echonest.com/api/v4/artist/search?api_key="+Echonest.api_key+"&results="+Echonest.results+"&sort=hotttnesss-desc&bucket=biographies&bucket=images&bucket=video&bucket=id:spotify";
		var json_response = AJAX.requestGET(url);
		var response = JSON.parse(json_response);
		Data.recomendaciones = response.response;
		Data.recLength = response.response.artists.length;
		return response;
	},
	startForm: function(){
		var myButton = document.getElementById("button");
		Listener.add(myButton, "click", Listener.eventSearch, false);
		var myBox = document.getElementById("box");
		Listener.add(myBox, "keypress", Listener.eventKeySearch, false);
	}
}

var Layout = {
	createContainer: function(id, element){
    	var container = document.createElement(element);
		container.id = id;
		return container;
    },
    createImage: function(idImg,src,alt,title,eventPlay){
    	var img = document.getElementById(idImg);
	    img.src = src;
	    img.alt = alt;
	    img.title = title;
	    img.style.visibility = 'visible';
	    img.addEventListener;
	    switch(eventPlay){
	    	case 1:
	    		Listener.add(img, "click", Listener.eventPlayRec, false);
	    		break;
	    	case 2:
	    		Listener.add(img, "click", Listener.eventPlayBus, false);
	    		break;
	    	case 3:
	    		Listener.add(img, "click", Listener.eventPlayPla, false);
	    		break;
	    }
    },
    setTitle: function(id,name,alt,eventPlay){
    	var text = document.getElementById(id);
    	text.innerHTML = name;
	    text.alt = alt;
	    text.title = name;
	    text.style.visibility = 'visible';
    	switch(eventPlay){
	    	case 1:
	    		Listener.add(text, "click", Listener.eventPlayRec, false);
	    		break;
	    	case 2:
	    		Listener.add(text, "click", Listener.eventPlayBus, false);
	    		break;
	    	case 3:
	    		Listener.add(text, "click", Listener.eventPlayPla, false);
	    		break;
	    }
    },
    renderRecomendadosContainer: function(){
		var j;
		var id = 1;
		var name;
		var artId;
		var url;
		if(Data.recLength == 0){
			j = 0;
		}else if(Data.recLength < 4){
			j = Data.recLength;
			Layout.cleanContainer("recomendacion");
		}else{
			j = 4;
		}
		for (var i = 0; i < j; i++) {
			if(Data.recomendaciones.artists[i].images.length > 0){
				artId = i;
				name = Data.recomendaciones.artists[i].name;
				url = Data.recomendaciones.artists[i].images[2].url;
				Layout.createImage('recomendacion'+id, url, artId, name, 1);
				Layout.setTitle('tituloRecomendacion'+id, name, artId, 1);
				id++;
			}else{
				j++;
			}
		};
    },
    cleanContainer: function(table){
    	for (var i = 1; i < 5; i++) {
			var img = document.getElementById(table+i);
			img.style.visibility = 'hidden';
		};
    },
    cleanSearchText: function(){
    	for (var i = 1; i < 5; i++) {
    		console.log('asd');
			var text = document.getElementById('tituloBusqueda'+i);
			text.style.visibility = 'hidden';
		};
    },
    renderSearchContainer: function(){
		var j;
		var id = 1;
		var name;
		var artId;
		var url;
		if(Data.busLength == 0){
			j = 0;
		}else if(Data.busLength < 4){
			j = Data.busLength;
			Layout.cleanContainer("busqueda");
			Layout.cleanSearchText();
		}else{
			j = 4;
		}
		for (var i = 0; i < j; i++) {
			if(Data.busqueda.artists[i].images.length > 1){
				artId = i;
				name = Data.busqueda.artists[i].name;
				url = Data.busqueda.artists[i].images[0].url;
				Layout.createImage('busqueda'+id, url, artId, name, 2);
				Layout.setTitle('tituloBusqueda'+id, name, artId, 2);
				id++;
			}else if(Data.busqueda.artists[i].images.length == 0){
			}else{
				j++;
			}
		};
    },
    renderYoutube: function(response){
    	var myDiv = document.getElementById('vid');
    	myDiv.removeChild(myDiv.childNodes[1]);
    	var i = 0;
        var found = false;
        while (!found && i < response.video.length){
            if(response.video[i].site == "youtube.com"){
	            found = true;
	        }else{
	        	i++;
	        }
        }
        if(found){
	        var myEmbed = document.createElement('embed');
	        var url = response.video[i].url;
	        url = url.replace("watch?v=", "v/");
	        myEmbed.src = url;
	        myDiv.insertBefore(myEmbed, myDiv.childNodes[1]);    
	        var myInfo = document.getElementById('info');
	        var info = "Artist: "+response.name+"<br><br>Title: "+response.video[i].title+"<br><br>Bio: "+response.biographies[1].text;
	        myInfo.innerHTML = info;
	        Song.id = response.id;
	        Song.name = response.name;
	        Song.url = response.video[i].title;
	        var spoty_id = response.foreign_ids[0].foreign_id;
	        spoty_id = spoty_id.replace("spotify:artist:", "");
	        Song.spoty_id = spoty_id;
	    }else{
	    	console.log("Video not found");
	    }
    },
    renderPlaylist: function(response){
    	var j;
		var id = 1;
		var name;
		var artId;
		var url;
		var response;
		Layout.cleanContainer('playlist');
		if(Data.plaLength == 0){
			j = 0;
		}else if(Data.plaLength < 4){
			j = Data.plaLength;
		}else{
			j = 4 + Playlist.position;
		}
		for (var i = Playlist.position; i < j; i++) {
			if(i < Data.plaLength){
				response = Search.searchArtist(Data.playlist.response[i].track_id);
				if(response.response.artist.images.length > 0){	
					artId = i;
					name = response.response.artist.name;
					url = response.response.artist.images[0].url;		
					var myImg = Layout.createImage('playlist'+id, url, artId, name, 3);
					myButton = document.getElementById("delete"+id);
					myButton.style.visibility = 'visible';
					myText = document.getElementById('titolPlay'+id);
					myText.innerHTML = name;
					id++;
				}else{
					j++;
				}
			}
		}
    },
    renderContainer: function(container){
		document.body.appendChild(container);
	},
	loadDeletes: function(){
		for(var i = 1; i < 5; i++){
			myButton = document.getElementById("delete"+i);
			myButton.style.visibility = 'hidden';
			myText = document.getElementById('titolPlay'+i);
			myText.innerHTML = "";
		}
	},
}

var Playlist = {
	position: 0,
	startButton: function(){
		var myButton = document.getElementById("addSong");
		Listener.add(myButton, "click", Listener.eventAddToPlaylist, false);
		myButton = document.getElementById("flecha1");
		Listener.add(myButton, "click", Listener.eventPlaylistUp, false);
		myButton = document.getElementById("flecha2");
		Listener.add(myButton, "click", Listener.eventPlaylistDown, false);
		myButton = document.getElementById("goSpotify");
		Listener.add(myButton, "click", Listener.eventGoToSpotify, false);
		position = 0;
		for(var i = 1; i < 5; i++){
			myButton = document.getElementById("delete"+i);
			Listener.add(myButton, "click", Listener.eventDeleteArtist, false);
		}
	},
	loadPlaylist: function(){
		var response = DB.getPlaylist();
		Data.playlist = response;
		Data.plaLength = response.response.length;
		Layout.loadDeletes();
		Layout.renderPlaylist();
	}
}

var Application = {
	start: function(){
		console.log('POU');
		window.onload = function(){
			var musicID = DB.getRandomSong();
			var name = DB.getSong(musicID);
			if(name == -1){
				var response = Search.searchTopArtists();
				Layout.renderRecomendadosContainer();
				Layout.renderYoutube(Data.recomendaciones.artists[0]);
			}else{
				Search.searchRecomendados(name.data[0].track_id);
				Layout.renderRecomendadosContainer();
				var response = Search.searchArtist(name.data[0].track_id);
				Layout.renderYoutube(response.response.artist);
			}
			Playlist.startButton();
			Playlist.loadPlaylist();
			Search.startForm();
			Layout.cleanContainer('busqueda');
			Layout.cleanSearchText();
			var timeOut = setTimeout(function () { }, 1000);
		}
	}
}

Listener.add(
	document,
	"DOMContentLoaded",
	Application.start(),
	capture = true
);
