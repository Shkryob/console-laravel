<?php

/*
 * This file is part of the lucid-console project.
 *
 * (c) Vinelab <dev@vinelab.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Lucid\Console\Commands;

use Illuminate\Console\GeneratorCommand;
use Lucid\Console\Filesystem;
use Lucid\Console\Finder;
use Lucid\Console\Str;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Charalampos Raftopoulos <harris@vinelab.com>
 */
class JobDeleteCommand extends GeneratorCommand
{
    use Finder;
    use Filesystem;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'delete:job';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete an existing Job in a domain';

    /**
     * The type of class being deleted.
     *
     * @var string
     */
    protected $type = 'Job';

    /**
     * Execute the console command.
     *
     * @return bool|null
     */
    public function fire()
    {
        try {
            $domain = studly_case($this->argument('domain'));
            $title = $this->parseName($this->argument('job'));
            $domainPath = $this->findDomainPath($domain).'/Jobs';

            if (!file_exists($job = $this->findJobPath($domain, $title))) {
                $this->error('Job class '.$title.' cannot be found.');
            } else {
                $this->deleteFile($job);

                if (count($this->checkDirectories($domainPath)) === 0) {
                    $this->deleteDirectory($domainPath);
                }

                $this->info('Job class <comment>'.$title.'</comment> deleted successfully.');
            }
        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function getArguments()
    {
        return [
            ['domain', InputArgument::REQUIRED, 'The domain from which the job will be deleted.'],
            ['job', InputArgument::REQUIRED, 'The job\'s name.'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    public function getStub()
    {
        return __DIR__.'/../Generators/stubs/job.stub';
    }

    /**
     * Parse the job name.
     *  remove the Job.php suffix if found
     *  we're adding it ourselves.
     *
     * @param string $name
     *
     * @return string
     */
    protected function parseName($name)
    {
        return Str::job($name);
    }
}
