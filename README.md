Teknoo Software - East Code Runner
==================================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/78509db7-2931-4a7f-a307-42680b4c24fe/mini.png)](https://insight.sensiolabs.com/projects/78509db7-2931-4a7f-a307-42680b4c24fe) [![Build Status](https://travis-ci.org/TeknooSoftware/east-code-runner.svg?branch=master)](https://travis-ci.org/TeknooSoftware/east-code-runner)

Code Runner is an universal package to execute a script (currently only PHP7 script, but the library and worker are 
designed to be compliant with any languages) into a secured and isolated environment. It's usable to allow visitor to 
execute some demo code from your websites to present yours solutions, or to manage execution of remote and async
operations.

The library has been designed in two parts :
* Tasks and runners managers to register and dispatch tasks to workers, usable via some HTTP API.
* Runner, execution task on a remote worker, communicating via an AMQP exchange.

This library follows :
- the PSR2 for the coding style.
- the PSR4 for autoloader.
- the PSR7 for HTTP message and enpoint
- the PSR11 draft for service definition.

This library provides also Symfony 3+ definition to be used directly into a Symfony environment.

Installation & Requirements
---------------------------
To install this library with composer, run this command :

    composer require teknoo/east-code-runner
    
For Symfony 3+ :

Declare into your AppKernel:
    
    new \Stof\DoctrineExtensionsBundle\StofDoctrineExtensionsBundle(),
    new Teknoo\East\FoundationBundle\EastFoundationBundle(),
    new OldSound\RabbitMqBundle\OldSoundRabbitMqBundle(),
    new Teknoo\East\CodeRunnerBundle\TeknooEastCodeRunnerBundle()


Add into your app/config/routing.yml:

    codeRunner:
        resource: '@TeknooEastCodeRunnerBundle/Resources/config/routing.yml'
        prefix: '/code-runner'

Add into your app/config/config.yml

    # Doctrine Configuration
    doctrine:
        //[...]
    
        orm:
            //[...]
            entity_managers:
                default:
                  //[...]
                  mappings:
                      TeknooEastCodeRunner:
                          type: 'yml'
                          dir: "%kernel.root_dir%/../vendor/teknoo/east-code-runner/src/universal/config/doctrine"
                          is_bundle: false
                          prefix: 'Teknoo\East\CodeRunner\Entity'
                  auto_mapping: false
                  filters:
                      softdeleteable:
                          class: 'Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter'
                          enabled: false

    #AMQP Configuration
    old_sound_rabbit_mq:
        connections:
            default:
                host:     '%You AMQP server%'
                port:     5672
                user:     '%your user%'
                password: '%your password%'
                vhost:    '/'
                lazy:     true
                connection_timeout: 3
                read_write_timeout: 3
    
        producers:
            #Only on server side
            remote_php7_task: #Exchange to send task from runner to its worker
                connection:       default
                exchange_options: {name: '%channel nam%', type: direct, auto_delete: false}
                service_alias:    'teknoo.east.bundle.coderunner.vendor.old_sound_producer.remote_php7.task' 
            #Only on worker side
            remote_php7_return: #Exchange to send task's result from worker to runner manager
                connection:       default
                exchange_options: {name: '%channel name%', type: direct, auto_delete: false}
                service_alias:    'teknoo.east.bundle.coderunner.vendor.old_sound_producer.remote_php7.return' 
    
        consumers:
            #Only on worker side
            worker_php7_task: #Consumer for worker to retrieve atsk
                connection: default
                exchange_options:
                    name: '%channel nam%'
                    type: direct
                    auto_delete: false
                queue_options:
                    name: '%channel nam%'
                    auto_delete: false
                callback: 'teknoo.east.bundle.coderunner.worker.php7_runner'
            #Only on server side
            consumer_php7_return: #Consumer to retrieve worker's results
                connection: default
                exchange_options:
                    name: '%channel nam%'
                    type: direct
                    auto_delete: false
                queue_options:
                    name: '%channel nam%'
                    auto_delete: false
                callback: 'teknoo.east.bundle.coderunner.service.rabbit_mq_return_consumer'
    
    #Enable gedmo doctine extension
    stof_doctrine_extensions:
        default_locale: en_US
        orm:
            default:
                timestampable: true
    
    #Configure package
    teknoo_east_code_runner:
        php7_runner:
          enable_server: true #To reference the RemotePHP7Runner into the Runner manager
          enable_worker: true #To enable RemotePHP7Worker (only worker side)
          composer_command: 'composer'
          php_command: 'php'
        tasks_managers:
            default: #You can define several tasks managers
                service_id: 'demo.task.manager'
                identifier: 'default_task_manager'
                url_pattern: 'http://localhost/app_dev.php/code-runner/task/UUID' #Needed to retrieve task's url
                is_default: true 
                
This library requires :

    * PHP 7+
    * Composer
    
Credits
-------
Richard Déloge - <richarddeloge@gmail.com> - Lead developer.
Teknoo Software - <http://teknoo.software>

About Teknoo Software
---------------------
**Teknoo Software** is a PHP software editor, founded by Richard Déloge. 
Teknoo Software's DNA is simple : Provide to our partners and to the community a set of high quality services or software,
 sharing knowledge and skills.

License
-------
Code Runner is licensed under the MIT License - see the licenses folder for details

Contribute :)
-------------

You are welcome to contribute to this project. [Fork it on Github](CONTRIBUTING.md)
