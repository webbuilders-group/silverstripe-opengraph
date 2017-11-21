<?php

namespace TractorCow\OpenGraph;




/**
 *
 * @author Damian Mooyman
 */
interface IOGMusicPlaylist extends IOGMusicSongList {
	
	/**
	 * The creator(s) of this object
	 * @return IOGProfile[]|IOGProfile|string[]|string Creator profile object(s) or url(s) to profile(s)
	 */
	function getOGMusicCreators();
}