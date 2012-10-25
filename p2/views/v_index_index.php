
<div class=centering>


<div id=welcome title="Welcome to MMMMicroblogger">
  <div class=logo>
  Welcome to <br><span class=name>MMMMicroblogger</span>
  </div>
  Get started!
  <a href="/users/login">Log In</a> or <a href="/users/signup">Sign Up</a> </li>
</div>

<div id=recent_posts class=stream>
<h2>Here's what people are saying:</h2>
<dl>
<? foreach($posts as $p) { ?>
  <dt> 
    <span class=user><?= $p->first_name ?></span>
    at <?= date('D M d, Y, h:ia', $p->created) ?>
  </dt>
  <dd>
    <?= $p->text ?>
  </dd>
<? } ?>
</dl>
</div>

<div id=context_help class=help>
  <span class="title">Welcome to MMMMicroblogger!</span>
  <dl>
  <dt>Why?</dt>
  <dd>This is where you can keep up with all the latest news from friends and interesting people around the internet.</dd>
  <dt>Get Started</dt>
  <dd>If you have an account, click "Log In" to go to your home page.  Or "Sign Up" to create an account now.</dd>
  </dl>
  Remember, you can always click "Need Help?" to get help on whatever page you're on.
</div>
