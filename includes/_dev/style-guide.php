<?php
/**
 * Style guide.
 */

get_header();
?>

<div class="container">

<p>The purpose of this HTML is to help determine what default settings are with CSS and to make sure that all possible HTML Elements are included in this HTML so as to not miss any possible Elements when designing a site.</p>

<hr />

<h1>Heading 1</h1>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h2>Heading 2</h2>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h3>Heading 3</h3>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h4>Heading 4</h4>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h5>Heading 5</h5>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h6>Heading 6</h6>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<hr />

<h1>Heading 1<br />with multiple lines</h1>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h2>Heading 2<br />with multiple lines</h2>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h3>Heading 3<br />with multiple lines</h3>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h4>Heading 4<br />with multiple lines</h4>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h5>Heading 5<br />with multiple lines</h5>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<h6>Heading 6<br />with multiple lines</h6>

<p>Single line of sample text to show spacing between paragraph and headline.</p>

<hr />

<h1>Heading 1</h1>
<h2>Heading 2 following H1</h2>
<h3>Heading 3 following H2</h3>
<h4>Heading 4 following H3</h4>
<h5>Heading 5 following H4</h5>

<hr />

<h2 id="paragraph">Paragraph</h2>

<p>Lorem ipsum dolor sit amet, <a title="testing link" href="https://google.com">testing link</a> adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>

<p>Lorem ipsum dolor sit amet, <em>emphasis</em> consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus.</p>

<hr />

<h2 id="buttons">Buttons</h2>

<p><a href="https://google.com" class="btn">Button Style</a></p>

<hr />

<h2 id="list_types">List Types</h2>

<h3>Definition List</h3>

<dl>
	<dt>Definition List Title</dt>
	<dd>This is a definition list division.</dd>
</dl>

<h3>Ordered List</h3>

<ol>
	<li>List Item 1</li>
	<li>List Item 2 - the first line<br>
		Second Line of an ordered line item</li>
	<li>List Item 3</li>
</ol>

<h3>Unordered List</h3>

<ul>
	<li>List Item 1</li>
	<li>List Item 2 - the first line<br>
		Second Line of an unordered line item</li>
	<li>List Item 3</li>
</ul>

<hr />

<h2 id="spinner">Spinner</h2>

<code>.spinner.spin</code>

<div class="spinner spin"></div>

<hr />

<h2 id="form_elements">Forms</h2>

<fieldset>

	<legend>Legend</legend>

	<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus.</p>

	<form>

		<h2>Form Element</h2>

		<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui.</p>

		<p><label for="text_field">Text Field:</label><br>
			<input id="text_field" type="text" placeholder="Placeholder"></p>

		<p><label for="text_area">Text Area:</label><br>
			<textarea id="text_area" placeholder="Placeholder"></textarea></p>

		<p><label for="select_element">Select Element:</label></p>

		<p><select name="select_element"><optgroup label="Option Group 1"><option value="1">Option 1</option><option value="2">Option 2</option><option value="3">Option 3</option></optgroup></select></p>

		<p><select name="select_element"><optgroup label="Option Group 2"><option value="1">Option 1</option><option value="2">Option 2</option><option value="3">Option 3</option></optgroup></select></p>

		<p><label for="radio_buttons">Radio Buttons:</label></p>

		<p><input class="radio" name="radio_button" type="radio" value="radio_1"> Radio 1<br>
			<input class="radio" name="radio_button" type="radio" value="radio_2"> Radio 2<br>
			<input class="radio" name="radio_button" type="radio" value="radio_3"> Radio 3</p>

		<p><label for="checkboxes">Checkboxes:</label></p>

		<p><input class="checkbox" name="checkboxes" type="checkbox" value="check_1"> Radio 1<br>
			<input class="checkbox" name="checkboxes" type="checkbox" value="check_2"> Radio 2<br>
			<input class="checkbox" name="checkboxes" type="checkbox" value="check_3"> Radio 3</p>

		<p><label for="password">Password:</label></p>

		<p><input class="password" name="password" type="password"></p>

		<p><label for="file">File Input:</label><br>
			<input class="file" name="file" type="file"></p>

		<p><input type="reset" value="Clear"> <input type="submit" value="Submit"></p>

	</form>

</fieldset>

<hr />

<h2 id="tables">Tables</h2>

<table cellspacing="0" cellpadding="0">
	<tbody>
		<tr>
			<th>Table Header 1</th>
			<th>Table Header 2</th>
			<th>Table Header 3</th>
		</tr>
		<tr>
			<td>Division 1</td>
			<td>Division 2</td>
			<td>Division 3</td>
		</tr>
		<tr class="even">
			<td>Division 1</td>
			<td>Division 2</td>
			<td>Division 3</td>
		</tr>
		<tr>
			<td>Division 1</td>
			<td>Division 2</td>
			<td>Division 3</td>
		</tr>
	</tbody>
</table>

<hr />

<h2 id="misc">Misc Stuff – abbr, acronym, pre, code, sub, sup, etc.</h2>

<p>Lorem <sup>superscript</sup> dolor <sub>subscript</sub> amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. <cite>cite</cite>. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. <acronym title="National Basketball Association">NBA</acronym> Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus. <abbr title="Avenue">AVE</abbr></p>

<p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Nullam dignissim convallis est. Quisque aliquam. Donec faucibus. Nunc iaculis suscipit dui. Nam sit amet sem. Aliquam libero nisi, imperdiet at, tincidunt nec, gravida vehicula, nisl. Praesent mattis, massa quis luctus fermentum, turpis mi volutpat justo, eu volutpat enim diam eget metus. Maecenas ornare tortor. Donec sed tellus eget sapien fringilla nonummy. <acronym title="National Basketball Association">NBA</acronym> Mauris a ante. Suspendisse quam sem, consequat at, commodo vitae, feugiat in, nunc. Morbi imperdiet augue quis tellus. <abbr title="Avenue">AVE</abbr></p>

<blockquote><p>“This stylesheet is going to help so freaking much.”<br>
-Blockquote</p></blockquote>

<h2>Blockquotes</h2>

<p>Single line blockquote:</p>

<blockquote><p>Stay hungry. Stay foolish.</p></blockquote>

<p>Multi line blockquote with a cite reference:</p>

<blockquote><p>People think focus means saying yes to the thing you’ve got to focus on. But that’s not what it means at all. It means saying no to the hundred other good ideas that there are. You have to pick carefully. I’m actually as proud of the things we haven’t done as the things I have done. Innovation is saying no to 1,000 things. <cite>Steve Jobs – Apple Worldwide Developers’ Conference, 1997</cite></p></blockquote>

<h2>Tables</h2>

<table>
	<tbody>
		<tr>
			<th>Employee</th>
			<th class="views">Salary</th>
			<th></th>
		</tr>
		<tr class="odd">
			<td><a href="http://example.com/">Jane</a></td>
			<td>$1</td>
			<td>Because that’s all Steve Job’ needed for a salary.</td>
		</tr>
		<tr class="even">
			<td><a href="http://example.com">John</a></td>
			<td>$100K</td>
			<td>For all the blogging he does.</td>
		</tr>
		<tr class="odd">
			<td><a href="http://example.com/">Jane</a></td>
			<td>$100M</td>
			<td>Pictures are worth a thousand words, right? So Tom x 1,000.</td>
		</tr>
		<tr class="even">
			<td><a href="http://example.com/">Jane</a></td>
			<td>$100B</td>
			<td>With hair like that?! Enough said…</td>
		</tr>
	</tbody>
</table>

<h2>Definition Lists</h2>

<dl>
	<dt>Definition List Title</dt>
	<dd>Definition list division.</dd>
	<dt>Startup</dt>
	<dd>A startup company or startup is a company or temporary organization designed to search for a repeatable and scalable business model.</dd>
	<dt>#dowork</dt>
	<dd>Coined by Rob Dyrdek and his personal body guard Christopher “Big Black” Boykins, “Do Work” works as a self motivator, to motivating your friends.</dd>
	<dt>Do It Live</dt>
	<dd>I'll let Bill O'Reilly will <a title="We'll Do It Live" href="https://www.youtube.com/watch?v=O_HyZ5aW76c">explain</a> this one.</dd>
</dl>

<h2>Unordered Lists (Nested)</h2>

<ul>
	<li>List item one
		<ul>
			<li>List item one
				<ul>
					<li>List item one</li>
					<li>List item two</li>
					<li>List item three</li>
					<li>List item four</li>
				</ul>
			</li>
			<li>List item two</li>
			<li>List item three</li>
			<li>List item four</li>
		</ul>
	</li>
	<li>List item two</li>
	<li>List item three</li>
	<li>List item four</li>
</ul>

<h2>Ordered List (Nested)</h2>

<ol>
	<li>List item one
		<ol>
			<li>List item one
				<ol>
					<li>List item one</li>
					<li>List item two</li>
					<li>List item three</li>
					<li>List item four</li>
				</ol>
			</li>
			<li>List item two</li>
			<li>List item three</li>
			<li>List item four</li>
		</ol>
	</li>
	<li>List item two</li>
	<li>List item three</li>
	<li>List item four</li>
</ol>

<h2>HTML Tags</h2>

<p>These supported tags come from the WordPress.com code <a title="Code" href="http://en.support.wordpress.com/code/">FAQ</a>.</p>

<p><strong>Address Tag</strong></p>

<address>1 Infinite Loop<br>
Cupertino, CA 95014<br>
United States</address>

<p><strong>Anchor Tag (aka. Link)</strong></p>

<p>This is an example of a <a title="Apple" href="http://apple.com">link</a>.</p>

<p><strong>Abbreviation Tag</strong></p>

<p>The abbreviation <abbr title="Seriously">srsly</abbr> stands for “seriously”.</p>

<p><strong>Acronym Tag</strong></p>

<p>The acronym <acronym title="For The Win">ftw</acronym> stands for “for the win”.</p>

<p><strong>Big Tag</strong></p>

<p>These tests are a <big>big</big> deal, but this tag is no longer supported in HTML5.</p>

<p><strong>Cite Tag</strong></p>

<p>“Code is poetry.” —<cite>Automattic</cite></p>

<p><strong>Code Tag</strong></p>

<p>You will learn later on in these tests that <code>word-wrap: break-word;</code> will be your best friend.</p>

<p><strong>Delete Tag</strong></p>

<p>This tag will let you <del>strikeout text</del>, but this tag is no longer supported in HTML5 (use the <code>&lt;strike&gt;</code> instead).</p>

<p><strong>Emphasize Tag</strong></p>

<p>The emphasize tag should <em>italicize</em> text.</p>

<p><strong>Insert Tag</strong></p>

<p>This tag should denote <ins>inserted</ins> text.</p>

<p><strong>Keyboard Tag</strong></p>

<p>This scarcely known tag emulates <kbd>keyboard text</kbd>, which is usually styled like the <code>&lt;code&gt;</code> tag.</p>

<p><strong>Preformatted Tag</strong></p>

<p>This tag styles large blocks of code.</p>

<pre>.post-title {
	margin: 0 0 5px;
	font-weight: bold;
	font-size: 38px;
	line-height: 1.2;
}</pre>

<p><strong>Quote Tag</strong></p>

<p><q>Developers, developers, developers…</q> –Steve Ballmer</p>

<p><strong>Strong Tag</strong></p>

<p>This tag shows <strong>bold<strong> text.</strong></strong></p>

<p><strong>Subscript Tag</strong></p>

<p>Getting our science styling on with H<sub>2</sub>O, which should push the “2” down.</p>

<p><strong>Superscript Tag</strong></p>

<p>Still sticking with science and Isaac Newton’s E = MC<sup>2</sup>, which should lift the 2 up.</p>

<p><strong>Teletype Tag</strong></p>

<p>This rarely used tag emulates <tt>teletype text</tt>, which is usually styled like the <code>&lt;code&gt;</code> tag.</p>

<p><strong>Variable Tag</strong></p>

<p>This allows you to denote <var>variables</var>.</p>

<h2>Image Alignment</h2>

<p>Welcome to image alignment! The best way to demonstrate the ebb and flow of the various image positioning options is to nestle them snuggly among an ocean of words. Grab a paddle and let’s get started.</p>

<p>On the topic of alignment, it should be noted that users can choose from the options of&nbsp;<em>None</em>,&nbsp;<em>Left</em>,&nbsp;<em>Right, </em>and&nbsp;<em>Center</em>. In addition, they also get the options of&nbsp;<em>Thumbnail</em>,&nbsp;<em>Medium</em>,&nbsp;<em>Large</em>&nbsp;&amp;&nbsp;<em>Fullsize</em>.</p>

<p style="text-align: center;"><?php image_tag( 'picsum', array( 'width' => 580, 'height' => 300, 'class' => 'size-full aligncenter', 'alt' => 'Image Alignment 580x300' ), array( 'random' => 1 ) ); ?></p>

<p>The image above happens to be&nbsp;<em><strong>centered</strong></em>.</p>

<p><strong><?php image_tag( 'picsum', array( 'width' => 150, 'height' => 150, 'class' => 'size-full alignleft', 'alt' => 'Image Alignment 150x150' ), array( 'random' => 1 ) ); ?></strong>The rest of this paragraph is filler&nbsp;for the sake of seeing the text wrap around the 150×150 image, which is <em><strong>left aligned</strong></em>.</p>

<p>As you can see the should be some space above, below, and to the right of the image. The text should not be creeping on the image. Creeping is just not right. Images need breathing room too. Let them speak like you words. Let them do their jobs without any hassle from the text. In about one more&nbsp;sentence&nbsp;here, we’ll see that the text moves from the right of the image down below the image in&nbsp;seamless&nbsp;transition. Again, letting the do it’s thang.&nbsp;Mission accomplished!</p>

<p>And now for a <em><strong>massively large image</strong></em>. It also has <em><strong>no alignment</strong></em>.</p>

<p><?php image_tag( 'picsum', array( 'width' => 1280, 'height' => 400, 'class' => 'alignnone', 'alt' => 'Image Alignment 1200x400' ), array( 'random' => 1 ) ); ?></p>

<p>The image above, though 1200px wide, should not overflow the content area. It should remain contained with no visible disruption to the flow of content.</p>

<p><?php image_tag( 'picsum', array( 'width' => 300, 'height' => 200, 'class' => 'size-full alignright', 'alt' => 'Image Alignment 300x200' ), array( 'random' => 1 ) ); ?></p>

<p>And now we’re going to shift things to the <em><strong>right align</strong></em>. Again, there should be plenty of room above, below, and to the left of the image. Just look at him there… Hey guy! Way to rock that right side. I don’t care what the left aligned image says, you look great. Don’t let anyone else tell you differently.</p>

<p>In just a bit here, you should see the text start to wrap below the right aligned image and settle in nicely. There should still be plenty of room and everything should be sitting pretty.&nbsp;Yeah… Just like that. It never felt so good to be right.</p>
<p>And just when you thought we were done, we’re going to do them all over again with captions!</p>

<div style="width: 590px" class="wp-caption aligncenter"><?php image_tag( 'picsum', array( 'width' => 580, 'height' => 300, 'class' => 'size-full', 'alt' => 'Image Alignment 580x300' ), array( 'random' => 1 ) ); ?><p class="wp-caption-text">Look at 580×300 getting some <a title="Image Settings" href="http://en.support.wordpress.com/images/image-settings/">caption</a> love.</p></div>

<p>The image above happens to be&nbsp;<em><strong>centered</strong></em>. The caption also has a link in it, just to see if it does anything funky.</p>

<div style="width: 160px" class="wp-caption alignleft"><?php image_tag( 'picsum', array( 'width' => 150, 'height' => 150, 'class' => 'size-full', 'alt' => 'Image Alignment 150x150' ), array( 'random' => 1 ) ); ?><p class="wp-caption-text">Itty-bitty caption.</p></div>

<p>The rest of this paragraph is filler&nbsp;for the sake of seeing the text wrap around the 150×150 image, which is <em><strong>left aligned</strong></em>.</p>

<p>As you can see the should be some space above, below, and to the right of the image. The text should not be creeping on the image. Creeping is just not right. Images need breathing room too. Let them speak like you words. Let them do their jobs without any hassle from the text. In about one more&nbsp;sentence&nbsp;here, we’ll see that the text moves from the right of the image down below the image in&nbsp;seamless&nbsp;transition. Again, letting the do it’s thang. Mission accomplished!</p>

<p>And now for a <em><strong>massively large image</strong></em>. It also has <em><strong>no alignment</strong></em>.</p>

<div style="width: 100%; max-width: 1200px" class="wp-caption alignnone"><?php image_tag( 'picsum', array( 'width' => 1200, 'height' => 400, 'alt' => 'Image Alignment 1200x400' ), array( 'random' => 1 ) ); ?><p class="wp-caption-text">Massive image comment for your eyeballs.</p></div>

<p>The image above, though 1200px wide, should not overflow the content area. It should remain contained with no visible disruption to the flow of content.</p>

<div style="width: 310px" class="wp-caption alignright"><?php image_tag( 'picsum', array( 'width' => 300, 'height' => 200, 'class' => 'size-full', 'alt' => 'Image Alignment 300x200' ), array( 'random' => 1 ) ); ?><p class="wp-caption-text">Feels good to be right all the time.</p></div>

<p>And now we’re going to shift things to the <em><strong>right align</strong></em>. Again, there should be plenty of room above, below, and to the left of the image. Just look at him there… Hey guy! Way to rock that right side. I don’t care what the left aligned image says, you look great. Don’t let anyone else tell you differently.</p>

<p>In just a bit here, you should see the text start to wrap below the right aligned image and settle in nicely. There should still be plenty of room and everything should be sitting pretty. Yeah… Just like that. It never felt so good to be right.</p>

<p>And that’s a wrap, yo! You survived the&nbsp;tumultuous&nbsp;waters of alignment. Image alignment achievement unlocked!</p>

</div>

<?php get_footer() ?>
