<?php
/**
 * Template Name: Swimming
 */
get_header();

// Optional order parsing (if your cards-grid uses it)
$order_str = (string) get_field('role_order');
$order_tokens = array_values(array_filter(array_map(function($t){
  $t = trim($t);
  $t = preg_replace('/^\(contains\)\s*/i', '', $t);
  $t = mb_strtolower($t);
  return $t;
}, preg_split('/[\r\n,]+/', $order_str) ?: [])));
natcasesort($order_tokens);
$order_tokens = array_values(array_unique($order_tokens));

?>

<div class="site-main template-swimming">

    <!-- Header -->
    <?php
    get_template_part(
        'template-parts/sections/two-column-section',
        null,
        [
            'image_alt'    => 'Swimmer',
        ]
    );
    ?>

















    <!-- TEMP -->
<?php
// --- Pathway & Development ---
$pathway_title = get_field('pathway_title') ?: 'Pathway & Development';
$pathway_text  = get_field('pathway_text') ?: '<p>Whether you’re just starting out or already competing at a high level, there is a pathway for every swimmer in Devon:</p>';
$pathway_items = get_field('pathway_items');

// Default items if none provided
if (!$pathway_items || !is_array($pathway_items)) {
  $pathway_items = [
    ['pathway_item_label' => 'Learn to Swim',            'pathway_item_desc' => 'building strong, confident skills in the water.'],
    ['pathway_item_label' => 'Club Training',            'pathway_item_desc' => 'joining a local swimming club for regular coaching and team spirit.'],
    ['pathway_item_label' => 'County Championships',     'pathway_item_desc' => 'testing your ability against the best swimmers in Devon.'],
    ['pathway_item_label' => 'Regional & National Competition', 'pathway_item_desc' => 'progressing to the next stage of your swimming journey.'],
  ];
}
$pathway_footer = get_field('pathway_footer') ?: 'Devon ASA also supports development through county training camps, coaching workshops, and officiating opportunities, ensuring a strong future for the sport.';
?>
<section class="section section--pathway" aria-labelledby="swim-pathway">
  <div class="container">
    <h2 id="swim-pathway"><?php echo esc_html($pathway_title); ?></h2>
    <div class="wysiwyg"><?php echo wp_kses_post($pathway_text); ?></div>
    <ul class="list">
      <?php foreach ($pathway_items as $item):
        $lbl = $item['pathway_item_label'] ?? '';
        $desc = $item['pathway_item_desc'] ?? '';
        if (!$lbl && !$desc) continue;
      ?>
        <li>
          <?php if ($lbl): ?><strong><?php echo esc_html($lbl); ?></strong><?php endif; ?>
          <?php if ($desc): ?> – <?php echo esc_html($desc); ?><?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
    <p><?php echo esc_html($pathway_footer); ?></p>
  </div>
</section>







    <section class="section section--pathway" aria-labelledby="swim-pathway">
        <div class="container">
            <h2 id="swim-pathway">Pathway &amp; Development</h2>
            <p>Whether you’re just starting out or already competing at a high level, there is a pathway for every swimmer in Devon:</p>
            <ul class="list">
                <li><strong>Learn to Swim</strong> – building strong, confident skills in the water.</li>
                <li><strong>Club Training</strong> – joining a local swimming club for regular coaching and team spirit.</li>
                <li><strong>County Championships</strong> – testing your ability against the best swimmers in Devon.</li>
                <li><strong>Regional &amp; National Competition</strong> – progressing to the next stage of your swimming journey.</li>
            </ul>
            <p>
                Devon ASA also supports development through county training camps, coaching workshops, and officiating opportunities, ensuring a strong future for the sport.
            </p>
        </div>
    </section>

    <section class="section section--competitions" aria-labelledby="swim-competitions">
        <div class="container">
            <h2 id="swim-competitions">Competitions</h2>
            <p>Each year, swimmers come together to compete in a range of events:</p>
            <ul class="list">
                <li><strong>Devon County Championships</strong> – the highlight of the county swimming calendar.</li>
                <li><strong>Open Meets &amp; Galas</strong> – giving swimmers of all levels the chance to gain experience and improve.</li>
            </ul>
            <p>
                Full details, including dates, venues, and entry requirements, are available on our
                <a href="<?php echo esc_url( site_url('/calendar') ); ?>">Calendar</a>. Competition results are shared after each event, celebrating the achievements of our swimmers.
            </p>
        </div>
    </section>

    <section class="section section--resources" aria-labelledby="swim-resources">
        <div class="container">
            <h2 id="swim-resources">Training &amp; Resources</h2>
            <p>
                Swimming isn’t just about racing — it’s about building healthy habits, teamwork, and confidence. We work with Swim England and the South West Region to provide resources for:
            </p>
            <ul class="list">
                <li><strong>Swimmers</strong> – stroke technique, nutrition, and wellbeing guidance.</li>
                <li><strong>Coaches</strong> – workshops and support for developing talent.</li>
                <li><strong>Officials &amp; Volunteers</strong> – training to keep competitions running smoothly and fairly.</li>
            </ul>
            <p>
                Explore further support via
                <a href="https://www.swimming.org/swimengland/" target="_blank" rel="noopener">Swim England’s resources</a>.
            </p>
        </div>
    </section>

    <section class="section section--success" aria-labelledby="swim-success">
        <div class="container">
            <h2 id="swim-success">Success Stories</h2>
            <p>
                Devon has a proud history of producing talented swimmers who go on to shine at regional, national, and international level. We regularly celebrate the achievements of our athletes, from medal winners at county events to those qualifying for national championships.
            </p>
            <p>Follow our news and social channels for the latest highlights and success stories.</p>
        </div>
    </section>

    <section class="section section--cta" aria-labelledby="swim-cta">
        <div class="container">
            <h2 id="swim-cta">Get Involved</h2>
            <ul class="list list--cta">
                <li><a class="link-cta" href="<?php echo esc_url( site_url('/clubs') ); ?>">Join a local club</a> to begin your journey.</li>
                <li><a class="link-cta" href="<?php echo esc_url( site_url('/calendar') ); ?>">Check the calendar</a> for upcoming events.</li>
                <li>Volunteer as an official or coach to help support the next generation of swimmers.</li>
            </ul>
        </div>
    </section>
    <!-- TEMP -->

























    <!-- Trophies -->
    <?php
    get_template_part('template-parts/grids/tab-grid', null, [
        'aria_label'      => 'Swimming',
        'sidebar_heading' => 'Trophies',
        'active'          => 'backstroke',
        'order_tokens'    => $order_tokens,
        'id_base'         => 'swimming',
        'tabs' => [
        ['slug'=>'backstroke',        'label'=>'Backstroke',        'panel'=>['type'=>'cards','prefix'=>'backstroke']],
        ['slug'=>'breaststroke',      'label'=>'Breaststroke',      'panel'=>['type'=>'cards','prefix'=>'breaststroke']],
        ['slug'=>'butterfly',         'label'=>'Butterfly',         'panel'=>['type'=>'cards','prefix'=>'butterfly']],
        ['slug'=>'freestyle',         'label'=>'Freestyle',         'panel'=>['type'=>'cards','prefix'=>'freestyle']],
        ['slug'=>'individual_medley', 'label'=>'Individual Medley', 'panel'=>['type'=>'cards','prefix'=>'individual_medley']],
        ['slug'=>'special_awards',    'label'=>'Special Awards',    'panel'=>['type'=>'cards','prefix'=>'special_awards']],

        ],
    ]);
    ?>
</div>

<?php get_footer(); ?>
