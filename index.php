<?php 

require('lib/OAuth.php');

include("partials/header.php");


if ($_POST) {
	$searched = true;

	$search_term = $_POST['search_term'];

	$cc_key = "YOUR_API_KEY";
	$cc_secret = "YOUR_SECRET";
	$url = "http://api.thenounproject.com/icons/" . $search_term;	
	$args = array();
	$args["limit_to_public_domain"] = 1;

	$consumer = new OAuthConsumer($cc_key, $cc_secret);

	$request = OAuthRequest::from_consumer_and_token($consumer, NULL,"GET", $url, $args);
	$request->sign_request(new OAuthSignatureMethod_HMAC_SHA1(), $consumer, NULL);
	
	$url = sprintf("%s?%s", $url, OAuthUtil::build_http_query($args));
	
	$ch = curl_init();
	
	$headers = array($request->to_header());
	
	curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
	
	$rsp = curl_exec($ch);
	$results = json_decode($rsp);
	
	$results = $results->icons;
} ?>

<header>
	<h1 class="text-center">Public Nouns</h1>
	<h2 class="text-center">Search <a href="//nounproject.com">The Noun Project</a> for public domain icons</h2>

	<form class="search small-12 large-6 columns small-centered" id="search" method="post" action="./">

		<input id="search_term" type="text" name="search_term" placeholder="Search Term">

		<input class="button" type="submit" value="Search">
	</form>
</header>

<div class="results small-12 medium-10 large-8 columns small-centered">
	<ul class="small-block-grid-3 medium-block-grid-6">

		<? if (isset($searched) && $searched == true) {
			if ($results != null) {
				echo "<h3 class='search-term'>Results for: {$search_term}</h3>";
				$icons = $results;
				foreach ($results as $result) { ?>
				<li class="icon">
					<a class="icon-link" href="//thenounproject.com<?= $result->permalink ?>" target="_blank">
						<img src="<?= $result->preview_url_84 ?>">
					</a>
					<span class="icon-name"><?= $result->term ?></span>

				</li>
				<? }
			} else {
				echo "<h3 class='search-term'>No results for: {$search_term}</h3>";

			}
		}

		?>
	</ul>
</div>

<? include("partials/footer.php"); 

