<div class="w-full md:w-8/12 lg:w-9/12">
    <div class="md:pl-5">
        <div class="flex flex-col sm:flex-row items-center sm:justify-between">
            <h2 class="text-2xl font-bold text-center sm:text-left">
                <?= $title ?>
                <span class="text-gray-400 font-semibold">(<?= date('Y', strtotime($isTv ? $video->first_air_date : $video->release_date)) ?>)</span>
            </h2>
            <?php $this->include('includes.video.parts.share') ?>
        </div>
        <section x-data="{tabOpen: 'details'}" class="mt-5 text-center sm:text-left">
            <?php $this
                ->include('includes.video.parts.tabs')
                ->include('includes.video.parts.details')
                ->include('includes.video.parts.season')
                ->include('includes.video.parts.clip')
                ->include('includes.video.parts.people')
                ->include('includes.video.similar')
            ?>
        </section>
    </div>
</div>