<?php
/**
 * gistless
 * replaces gist embeds in markdown files with the code in those embeds
 *
 * @author jenn schiffer
 * @url http://github.com/jennschiffer/gistless
 */

// github client/secret --- go to https://github.com/settings/applications/new
// to create/register an app and get these values
$clientId = '';
$secret = '';

// in which directory are the markdown posts
$mdDir = '';

// script page header
echo '<h1>~*gistless*~</h1>';
echo '<h2>good luck! xoxo jenn</h2>';

// get all files
$dir = new DirectoryIterator(dirname(__FILE__) . $mdDir);
$count = 0;

// for each .md or .markdown file, look for gist embed
foreach ($dir as $fileInfo) {
  if (!$fileInfo->isDot() && $fileInfo->isReadable() && (strtolower($fileInfo->getExtension()) === 'md' || strtolower($fileInfo->getExtension()) === 'markdown')) {
    findReplaceGists($fileInfo->getPathname());
    echo '<hr />';
  }
}

echo '<b>Total gists replaced: ' . $count . '</b>';

/*
* finds gist embed within file and replaces it
*
* @param file $file
*/
function findReplaceGists($file) {
  global $count, $clientId, $secret;
  $content = file_get_contents($file);
  $lines = explode("\n", $content);

  for ($i = 0; $i <= sizeof($lines); $i++) {
    $thisLine = $lines[$i];

    $gistScript = strstr($thisLine, '<script src="https://gist');

    if ( $gistScript ) {

      $gistUrl = strstr($gistScript, 'https://gist');

      if ( $gistUrl ) {
        $count++;

        $gistUrl = explode('"', $gistUrl)[0];
        $gistUrlTokens = explode('/', $gistUrl);

        // get token with gist id
        if ( is_numeric(substr($gistUrlTokens[3], 0, 1)) ) {
          $gistIdToken = $gistUrlTokens[3];
        }
        else {
          $gistIdToken = $gistUrlTokens[4];
        }

        // extract gist id
        $gistId = explode('.', $gistIdToken)[0];

        // extract file
        $gistFile = strstr($gistIdToken, 'file=');
        $gistFile = substr($gistFile, 5);

        $apiUrl = 'https://api.github.com/gists/' . $gistId;
        $response = json_decode(getGist($apiUrl . '?client_id=' . $clientId . '&client_secret=' . $secret), true);

        // get file
        if ( !$gistFile ) {
          $thisFile = reset($response['files']);
        }
        else {
          $thisFile = $response['files'][$gistFile];
        }

        // save the language
        $gistLanguage = strtolower($thisFile['language']);

        // save the content
        $gistContent = $thisFile['content'];

        // create the caption
        $gistCaption = '';
        if ( $gistFile ) {
          $gistCaption = '<p class="caption">' . $gistFile . '</p>';
        }

        // convert to html
        $gistHTML = '<div class="code-snippet">' . $gistCaption . '<pre lang="' . $gistLanguage . '"><code>' . htmlentities($gistContent) . '</code></pre></div>';

        // replace line with gist html
        $lines[$i] = $gistHTML;

        // write to file
        $newContent = implode($lines, "\n");
        file_put_contents($file, $newContent);

        // print url that was posted
        echo '<p>file: ' . $file . '<br />gist: ' . $gistUrl . '<br />' . $gistHTML . '</p>';
      }
    }
  }
}

/*
* uses curl to get the content of a gist
*
* @param string $gistUrl
* @return string
*/
function getGist($gistUrl) {
  $curl = curl_init();

  curl_setopt($curl,CURLOPT_USERAGENT,'noiseeee');
  curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
  curl_setopt($curl, CURLOPT_URL, $gistUrl);

  $result = curl_exec($curl);
  curl_close($curl);
  return $result;
}

?>
