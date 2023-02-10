<?php
    $members = null;
    if($args['member_id']) $members = get_member($args['member_id']);
    elseif($args['team_id']) $members = get_members_by_team($args['team_id']);
    
    if(!$members) $members = get_members($args['limit']);

    $active_team = ($args['type'] === "teams") ? get_team($args['team_id']) : null;
    $active_member = ($args['member_id']) ? $members->getTitle() : null;
    $is_bio = (!is_bool($args['is_bio'])) ? $args['is_bio'] === "true" : $args['is_bio'];
    $members = !is_array($members) ? [$members] : $members;
    $members = array_values(array_filter($members, function($m) use($args){ return !in_array($m->getId(), explode(",", $args['excludes'])); }));
    global $scripts;
?>
<?php if(count($members) > 0): ?>
    <?php switch($args['style']){
        case "slider": ?>
            <?php if($active_team): ?>
                <h2 class="upper bold center"><?php echo $active_team->name; ?></h2>
            <?php elseif($active_member): ?>
                <h2 class="upper bold center"><?php echo $active_member; ?></h2>
            <?php else: ?>
                <ul class="flex research">
                    <li class="button outline" data-id="0" data-value="all"><?php _ste("All"); ?></li>
                    <?php foreach (get_teams() as $kt => $vt) : ?>
                        <li class="button outline" data-id="<?php echo $vt->term_id; ?>" data-value="<?php echo strtolower($vt->name); ?>"><?php echo $vt->name; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <ul class="slider center slider-container">
                <?php foreach ($members as $key => $m) : ?>
                    <?php
                        $teams_name = implode(", ",array_map(function($t){ return $t->name; }, $m->getTeams()));
                    ?>
                    <li data-id="<?php echo $m->getID(); ?>" data-type="<?php echo strtolower($teams_name); ?>" data-role="<?php echo strtolower($m->getRole()); ?>" data-url="<?php echo $m->getUrl(); ?>" title="<?php echo $m->getTitle(); ?>" class="research-item slider-posts-item <?php echo ($key === 0) ? 'active' : ''; ?>" draggable="true">
                        <figure>
                            <img class='lp-img' src="<?php echo $m->getImgUrl(); ?>" alt="<?php echo $m->getTitle(); ?>">
                            <figcaption>
                                <h3><?php echo $m->getFirstname()."<br>".$m->getLastname(); ?></h3>
                                <p class="small italic roles"><?php echo $m->getRole(); ?></p>
                            </figcaption>
                        </figure>
                    </li>
                <?php endforeach; ?>
            </ul>
            <?php if($is_bio): ?>
                <a href="<?php echo $members[0]->getUrl(); ?>" class="button outline iauto btn-member" style="display: block; max-width: 175px;" title="<?php echo $members[0]->getTitle(); ?>"><?php _ste("Read bio"); ?></a>
            <?php endif; ?>
            <?php $script_name = "members-slider-script"; ?>
            <?php if(!in_array($script_name, $scripts)): ?>
                <script id="<?php echo $script_name; ?>">
                    load(() => {
                        if(getQueries(".slider-posts")){
                            let items = getQueries(".slider-container .slider-posts-item")
                            items.click(async i => {
                                await i.target.closest(".slider-container").getQueries(".slider-posts-item").forEach(it => it.setClassList(i.target.getAttribute("data-id") === it.getAttribute("data-id"), "active"))
                                const BTN = i.target.closest(".slider-posts").getQuery(".btn-member")
                                if(BTN){
                                    BTN.setAttribute("href", i.target.getAttribute("data-url"))
                                    BTN.setAttribute("title", i.target.getAttribute("title"))
                                }
                                setLeftPos(i.target.closest(".slider-container"))
                            })
                            items.watchAttr("class", (mutation, attribute) => { setLeftPos(mutation.target.closest(".slider-container")) })
                            let posDragX = 0;
                            items.action("touchstart", dragStart)
                            items.action("touchend", dragEnd)

                            function dragStart(event){ posDragX = (event.type === "dragstart") ? event.pageX : event.changedTouches[0].pageX }
                            function dragEnd(event){
                                let actualPosX = (event.type === "dragend") ? event.pageX : event.changedTouches[0].pageX
                                if(!posDragX.between(actualPosX-50,actualPosX+50)){
                                    let siblingElement = (actualPosX < posDragX) ? event.target.nextElementSibling : event.target.previousElementSibling;
                                    if(siblingElement){
                                        event.target.classList.remove("active")
                                        siblingElement.classList.add("active")
                                    }
                                }
                            }

                            function setLeftPos(parent = null){
                                parent = (parent) ? [parent] : getQueries(".slider-container")
                                parent.forEach(container => {
                                    let slider = container.getQuery(".active")
                                    container.style.setProperty("--left-pos", `calc((50vw - ${slider.offsetLeft}px) - ${slider.offsetWidth * .5}px)`)
                                })
                            }
                            setLeftPos()
                        }
                    })
                </script>
                <?php array_push($scripts, $script_name); ?>
            <?php endif; ?>
        <?php break;
        case 'list': ?>
            <ul class="list center list-container sport-container">
                <?php foreach($members as $m): ?>
                    <li data-id="<?php echo $m->getID(); ?>" data-type="<?php echo $m->getType(); ?>" class="list-posts-item">
                        <figure class="flex justify-start">
                            <img src="<?php echo $m->getImgUrl(); ?>" alt="<?php echo $m->getTitle(); ?>">
                            <figcaption class="left">
                                <h4 class="title small capitalize"><?php echo $m->getRole(); ?></h4>
                                <h3 class="title bold"><?php echo $m->getFullname(); ?></h3>
                                <h4 class="title italic small"><?php echo implode(", ",array_map(function($t){ return $t->name; }, $m->getTeams())); ?></h4>
                            </figcaption>
                        </figure>
                        <a href="<?php echo $m->getUrl(); ?>" class="button"><?php _ste("Read"); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php break;
    } ?>
<?php endif; ?>