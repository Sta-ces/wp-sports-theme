<?php
    if(!function_exists("createType")){
        function createType($post, $echo = true){
            $rslt = preg_replace("/s$/","",_st($post->getType()));
            $header = $post->getPrefix();
            if(!empty($header)) $rslt = $header;
            if($echo) echo $rslt;
            return $rslt;
        }
    }

    if(!function_exists("createTitle")){
        function createTitle($post, $echo = true){
            $rslt = "";
            if($post->isMatch()){
                $rslt .= "<span class='carybe-font'>vs</span> ";
                $rslt .= $post->getOpponent();
            }
            else $rslt .= $post->getTitle();
            if($echo) echo $rslt;
            return $rslt;
        }
    }
    
    $all_types = explode(",", $args['type']);
    $activities = get_activities($all_types, $args['limit']);
    $activities = array_values(array_filter($activities, function($a) use($args){ return !in_array($a->getId(), explode(",", $args['excludes'])) && (empty($args['team_id']) || in_array($a->getTeam()->term_id, explode(",", $args['team_id']))); }));
    $json_posts = [];
    global $scripts;
?>
<?php if(!empty($activities)): ?>
    <?php
        switch($args['style']){
            case 'header':?>
                <section class="header-posts-active sport-posts-active" data-id="<?php echo $activities[0]->getID(); ?>">
                    <h2 class="type carybe-font noselect"></h2>
                    <h1 class="title"></h1>
                    <p class="info italic"></p>
                </section>
                <ul class='flex justify-around center header-container sport-container'>
                    <?php foreach($activities as $a): ?>
                        <?php
                            $json = new stdClass();
                            $json->id = $a->getID();
                            $json->title = createTitle($a, false);
                            $json->type = createType($a, false);
                            $json->info = $a->getFullDate();
                            $json->img = $a->getImgUrl();
                            array_push($json_posts, $json);
                        ?>
                        <li data-id="<?php echo $a->getID(); ?>" class="header-posts-item sport-posts-item">
                            <a href="<?php echo $a->getUrl(); ?>">
                                <figure class='lp-content'>
                                    <img class='lp-img' src="<?php echo $a->getImgUrl(); ?>" alt="<?php echo $a->getTitle(); ?>">
                                    <figcaption>
                                        <h3><?php echo $json->title; ?></h3>
                                        <h4 class="small center"><?php echo $a->getTeamName(); ?></h4>
                                    </figcaption>
                                </figure>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php $script_name = "activity-header-script"; ?>
                <?php if(!in_array($script_name, $scripts)): ?>
                    <script id="<?php echo $script_name; ?>">
                        load(() => {
                            const POSTS = <?php echo json_encode($json_posts); ?>;
                            const POSTACTIVE = getQueries(".header-posts .header-posts-active");
                            const POSTITEM = getQueries(".header-posts .header-posts-item");
                            const BG = getQueries("<?php echo $args['bg']; ?>");
                            POSTITEM.action("mouseenter", (e) => {
                                const ITEM = e.target
                                const ID = ITEM.getAttribute("data-id")
                                const INFOBOX = ITEM.closest(".header-posts").getQuery(".header-posts-active")
                                INFOBOX.setAttribute("data-id", ID)
                            })

                            POSTACTIVE.watchAttr("data-id", (m, n) => {
                                const ITEM = m.target
                                const ID = parseInt(ITEM.getAttribute(n))
                                const POST = POSTS.find(p => p.id === ID)
                                displayInfo(ITEM, POST)
                            })

                            function displayInfo(item, post){
                                POSTITEM.forEach(pi => {
                                    let action = (parseInt(pi.getAttribute("data-id")) === post.id) ? "add" : "remove"
                                    pi.classList[action]("selected")
                                })
                                if(BG) BG.forEach(i => i.style.backgroundImage = `url('${post.img}')`)
                                item.getQuery(".type").html(post.type)
                                item.getQuery(".title").html(post.title)
                                item.getQuery(".info").html(post.info)
                            }

                            POSTACTIVE.forEach(item => displayInfo(item, POSTS[0]))
                        })
                    </script>
                    <?php array_push($scripts, $script_name); ?>
                <?php endif; ?>
            <?php break;
            default:
            case 'grid': ?>
                <?php if(count($all_types) > 1): ?>
                    <ul class="research sport-research flex center">
                        <li class="button outline" data-value="all"><?php _ste("All"); ?></li>
                        <?php foreach ($all_types as $kt => $vt) : ?>
                            <li class="button outline" data-value="<?php echo $vt; ?>"><?php _ste($vt); ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <h2 class="upper bold center"><?php _ste($all_types[0]); ?></h2>
                <?php endif; ?>
                <ul class="grid center grid-container sport-container">
                    <?php foreach($activities as $a): ?>
                        <li data-id="<?php echo $a->getID(); ?>" data-type="<?php echo $a->getType(); ?>" class="grid-posts-item research-item sport-posts-item">
                            <a href="<?php echo $a->getUrl(); ?>">
                                <figure class='lp-content'>
                                    <img class='lp-img' src="<?php echo $a->getImgUrl(); ?>" alt="<?php echo $a->getTitle(); ?>">
                                    <h2 class="ping-date"><?php
                                        $d = createDate($a->getDateStart(), "d M", false);
                                        echo html_map(explode(' ', $d));
                                    ?></h2>
                                    <figcaption>
                                        <h3><?php createTitle($a); ?></h3>
                                        <h4 class="small center no-marg"><?php echo $a->getTeamName(); ?></h4>
                                    </figcaption>
                                </figure>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php break;
            case 'list': ?>
                <ul class="list center list-container sport-container">
                    <?php foreach($activities as $a): ?>
                        <li data-id="<?php echo $a->getID(); ?>" data-type="<?php echo $a->getType(); ?>" class="list-posts-item">
                            <figure class="flex justify-start">
                                <img src="<?php echo $a->getImgUrl(); ?>" alt="<?php echo $a->getTitle(); ?>">
                                <h2 class="ping-date"><?php
                                    $d = createDate($a->getDateStart(), "d M", false);
                                    echo html_map(explode(' ', $d));
                                ?></h2>
                                <figcaption class="left">
                                    <h4 class="title small capitalize">
                                    <?php
                                        $type = "";
                                        $type .= "<strong>".createType($a, false)."</strong>";
                                        if($a->getTeamName()) $type .= " | ".$a->getTeamName();
                                        echo $type;
                                    ?></h4>
                                    <h3 class="title bold"><?php createTitle($a, true); ?></h3>
                                    <h4 class="title italic small activities-informations"><?php echo $a->getFullDate(false); ?></h4>
                                    <h4 class="title italic small activities-informations"><?php echo ($a->isHome()) ? get_infoth("address") : $a->getLocation(); ?></h4>
                                </figcaption>
                            </figure>
                            <a href="<?php echo $a->getUrl(); ?>" class="button"><?php _ste("Read"); ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php break;
        }
    ?>
<?php endif; ?>