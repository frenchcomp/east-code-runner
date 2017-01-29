#Teknoo Software - Code Runner - Change Log

##[0.0.1-alpha13] - 2017-01-29
###Fixed
- Complete test and fix TeknooEastCodeRunnerExtension to not ignore composer instruction configuration 

###Added 
- Documentation

##[0.0.1-alpha12] - 2017-01-24

###Updated
- Code runner package is now able to update Doctrine configuration to avoid complex configuration of final platform
 about this package.
- Code runner package support now several instance of a same Runner (with different services id) with their  
- Code runner package is now able to configure OldSound RabbitMQ bundle for each runner to avoid complex configuration
 of final platform about this package.
- Fix some bugs

##[0.0.1-alpha11] - 2017-01-17

###Updated
- Documentation

###Fixed
- Code style

##[0.0.1-alpha10] - 2017-01-17

###Added 
- Add end point to pass to next tasks if there are some issues

##[0.0.1-alpha9] - 2017-01-17

###Updated
- Documentation
- Unify AMQP exchanges to return Result and Statues in an unique exchange to limit memory
consumption and avoid result fetched after the final status. 

###Added
- Status can have an optional attribute to indicate the final status of the task.

###Fixed
- Several bugs into global workflow
- Cycling dependencies between some managers, registries and endpoint.
- Code style
- Symfony declaration
- Issue into RemotePHP7Runner issues with the Shell wrapper

#[0.0.1-alpha8] - 2017-01-13

###Updated
- PSR11 service provider

###Fixed
- Code style fixes

##[0.0.1-alpha7] - 2017-01-13

###Fixed
- Bug into Endpoint to delete tasks

##[0.0.1-alpha6] - 2017-01-13

###Fixed
- Some bug into persisting tasks
- Code style fixes

##[0.0.1-alpha5] - 2017-01-10

###Added
- Compiler pass for Symfony easier configuration.
- Symfony declaration
- Complete tests

###Fixed
- Several bug into registry to not fetch deleted elements.
- Bad uses of registries into managers.
- Some mistakes

##[0.0.1-alpha4] - 2017-01-07

###Fixed
- Bug into Task's serialisation  and persisting
- Bug into Runner manager with registry uses

##[0.0.1-alpha3] - 2017-01-06

###Updated
- Requirement to use last Teknoo States library stable and last East foundation library.

##[0.0.1-alpha2] - 2016-12-28

###Fixed
- bugs from Doctrine schema creation

###Add
- End point to interact with the library via an HTTP API.

##[0.0.1-alpha1] - 2016-12-16
- First alpha release

###Add
- Define interfaces
- Write tests
- Create value objects classes (Code, Status, Result)
- Create entities classes (Task, TaskRegistration, TaskExecution, TaskStandby)
- Create registries
- Create Tasks manager
- Create Runners manager
- Create definition
