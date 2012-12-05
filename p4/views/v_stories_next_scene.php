
The story has <?= $story->n_scenes() ?> scenes. <br>

<h2> Current Scene: <?= $scene->title ?> </h2>
<?= var_dump($scene); ?> <br>

<h2> Current Story </h2>
<?= var_dump($story); ?> <br>

<a href="/stories/next_scene">Move to the next scene</a>

<div id=context_help class=help>
  <span class="title">The Story Continues</span>
  <dl>
  <dt> The Story </dt>
  <dd> </dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
