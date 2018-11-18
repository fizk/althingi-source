<?php
return array (
  'service_manager' => 
  array (
    'aliases' => 
    array (
      'HttpRouter' => 'Zend\\Router\\Http\\TreeRouteStack',
      'router' => 'Zend\\Router\\RouteStackInterface',
      'Router' => 'Zend\\Router\\RouteStackInterface',
      'RoutePluginManager' => 'Zend\\Router\\RoutePluginManager',
      'HydratorManager' => 'Zend\\Hydrator\\HydratorPluginManager',
      'InputFilterManager' => 'Zend\\InputFilter\\InputFilterPluginManager',
      'console' => 'ConsoleAdapter',
      'Console' => 'ConsoleAdapter',
      'ConsoleDefaultRenderingStrategy' => 'Zend\\Mvc\\Console\\View\\DefaultRenderingStrategy',
      'ConsoleRenderer' => 'Zend\\Mvc\\Console\\View\\Renderer',
      'Zend\\Form\\Annotation\\FormAnnotationBuilder' => 'FormAnnotationBuilder',
      'Zend\\Form\\Annotation\\AnnotationBuilder' => 'FormAnnotationBuilder',
      'Zend\\Form\\FormElementManager' => 'FormElementManager',
      'ValidatorManager' => 'Zend\\Validator\\ValidatorPluginManager',
    ),
    'factories' => 
    array (
      'Zend\\Router\\Http\\TreeRouteStack' => 'Zend\\Router\\Http\\HttpRouterFactory',
      'Zend\\Router\\RoutePluginManager' => 'Zend\\Router\\RoutePluginManagerFactory',
      'Zend\\Router\\RouteStackInterface' => 'Zend\\Router\\RouterFactory',
      'Zend\\Cache\\PatternPluginManager' => 'Zend\\Cache\\Service\\PatternPluginManagerFactory',
      'Zend\\Cache\\Storage\\AdapterPluginManager' => 'Zend\\Cache\\Service\\StorageAdapterPluginManagerFactory',
      'Zend\\Cache\\Storage\\PluginManager' => 'Zend\\Cache\\Service\\StoragePluginManagerFactory',
      'FilterManager' => 'Zend\\Filter\\FilterPluginManagerFactory',
      'Zend\\Hydrator\\HydratorPluginManager' => 'Zend\\Hydrator\\HydratorPluginManagerFactory',
      'Zend\\InputFilter\\InputFilterPluginManager' => 'Zend\\InputFilter\\InputFilterPluginManagerFactory',
      'ConsoleAdapter' => 'Zend\\Mvc\\Console\\Service\\ConsoleAdapterFactory',
      'ConsoleExceptionStrategy' => 'Zend\\Mvc\\Console\\Service\\ConsoleExceptionStrategyFactory',
      'ConsoleRouteNotFoundStrategy' => 'Zend\\Mvc\\Console\\Service\\ConsoleRouteNotFoundStrategyFactory',
      'ConsoleRouter' => 'Zend\\Mvc\\Console\\Router\\ConsoleRouterFactory',
      'ConsoleViewManager' => 'Zend\\Mvc\\Console\\Service\\ConsoleViewManagerFactory',
      'Zend\\Mvc\\Console\\View\\DefaultRenderingStrategy' => 'Zend\\Mvc\\Console\\Service\\DefaultRenderingStrategyFactory',
      'Zend\\Mvc\\Console\\View\\Renderer' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'FormAnnotationBuilder' => 'Zend\\Form\\Annotation\\AnnotationBuilderFactory',
      'FormElementManager' => 'Zend\\Form\\FormElementManagerFactory',
      'Zend\\Validator\\ValidatorPluginManager' => 'Zend\\Validator\\ValidatorPluginManagerFactory',
      'MessageStrategy' => 'Rend\\View\\Strategy\\MessageFactory',
    ),
    'abstract_factories' => 
    array (
      0 => 'Zend\\Cache\\Service\\StorageCacheAbstractServiceFactory',
      1 => 'Zend\\Form\\FormAbstractServiceFactory',
    ),
    'delegators' => 
    array (
      'ControllerManager' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ControllerManagerDelegatorFactory',
      ),
      'Request' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleRequestDelegatorFactory',
      ),
      'Response' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleResponseDelegatorFactory',
      ),
      'Zend\\Router\\RouteStackInterface' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Router\\ConsoleRouterDelegatorFactory',
      ),
      'Zend\\Mvc\\SendResponseListener' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleResponseSenderDelegatorFactory',
      ),
      'ViewHelperManager' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ConsoleViewHelperManagerDelegatorFactory',
      ),
      'ViewManager' => 
      array (
        0 => 'Zend\\Mvc\\Console\\Service\\ViewManagerDelegatorFactory',
      ),
    ),
  ),
  'route_manager' => 
  array (
  ),
  'router' => 
  array (
    'routes' => 
    array (
      'index' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\IndexController',
            'action' => 'index',
          ),
        ),
      ),
      'thingmal-current' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/thingmal/nuverandi',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\HighlightController',
            'action' => 'get-active-issue',
          ),
        ),
      ),
      'loggjafarthing-current' => 
      array (
        'type' => 'Zend\\Router\\Http\\Literal',
        'options' => 
        array (
          'route' => '/loggjafarthing/nuverandi',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\HighlightController',
            'action' => 'get-current-assembly',
          ),
        ),
      ),
      'loggjafarthing' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/loggjafarthing[/:id]',
          'constraints' => 
          array (
            'id' => '[0-9]*',
          ),
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\AssemblyController',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'thingmenn' => 
          array (
            'type' => 'Zend\\Router\\Http\\Literal',
            'options' => 
            array (
              'route' => '/thingmenn',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\CongressmanController',
                'action' => 'assembly',
              ),
            ),
            'may_terminate' => true,
            'child_routes' => 
            array (
              'raedutimar-allir' => 
              array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => 
                array (
                  'route' => '/raedutimar',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-times',
                  ),
                ),
              ),
              'fyrirspurnir' => 
              array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => 
                array (
                  'route' => '/fyrirspurnir',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-questions',
                  ),
                ),
              ),
              'thingsalyktanir' => 
              array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => 
                array (
                  'route' => '/thingsalyktanir',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-resolutions',
                  ),
                ),
              ),
              'lagafrumvarp' => 
              array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => 
                array (
                  'route' => '/lagafrumvorp',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-bills',
                  ),
                ),
              ),
              'thingseta' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/:congressman_id/thingseta',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-sessions',
                  ),
                ),
              ),
              'thingmal' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/:congressman_id/thingmal',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-issues',
                  ),
                ),
              ),
              'thingmal-samantekt' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/:congressman_id/thingmal-samantekt',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-issues-summary',
                  ),
                ),
              ),
              'atvaedagreidslur' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/:congressman_id/atvaedagreidslur',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-voting',
                  ),
                ),
              ),
              'malaflokkar' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/:congressman_id/malaflokkar',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-categories',
                  ),
                ),
              ),
              'atvaedagreidslur-malaflokkar' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/:congressman_id/atvaedagreidslur-malaflokkar',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CongressmanController',
                    'action' => 'assembly-vote-categories',
                  ),
                ),
              ),
            ),
          ),
          'forsetar' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/forsetar[/:congressman_id]',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\PresidentAssemblyController',
                'identifier' => 'congressman_id',
              ),
            ),
            'may_terminate' => true,
          ),
          'samantekt' => 
          array (
            'type' => 'Zend\\Router\\Http\\Literal',
            'options' => 
            array (
              'route' => '/samantekt',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\AssemblyController',
                'action' => 'statistics',
              ),
            ),
          ),
          'raduneyti' => 
          array (
            'type' => 'Zend\\Router\\Http\\Literal',
            'options' => 
            array (
              'route' => '/raduneyti',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\CabinetController',
                'action' => 'assembly',
              ),
            ),
          ),
          'thingfundir' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/thingfundir[/:plenary_id]',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\PlenaryController',
                'identifier' => 'plenary_id',
              ),
            ),
          ),
          'bmal' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/bmal[/:issue_id]',
              'constraints' => 
              array (
                'issue_id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\UndocumentedIssueController',
                'identifier' => 'issue_id',
              ),
            ),
            'may_terminate' => true,
          ),
          'thingmal' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/thingmal[/:issue_id]',
              'constraints' => 
              array (
                'issue_id' => '[0-9]+',
              ),
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\IssueController',
                'identifier' => 'issue_id',
              ),
            ),
            'may_terminate' => true,
            'child_routes' => 
            array (
              'thingmal-raedutimar' => 
              array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => 
                array (
                  'route' => '/raedutimar',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\IssueController',
                    'action' => 'speech-times',
                  ),
                ),
                'may_terminate' => true,
              ),
              'thingraedur' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/raedur[/:speech_id]',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\SpeechController',
                    'identifier' => 'speech_id',
                  ),
                ),
              ),
              'ferli' => 
              array (
                'type' => 'Zend\\Router\\Http\\Literal',
                'options' => 
                array (
                  'route' => '/ferli',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\IssueController',
                    'action' => 'progress',
                  ),
                ),
              ),
              'efnisflokkar' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/efnisflokkar[/:category_id]',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\IssueCategoryController',
                    'identifier' => 'category_id',
                  ),
                ),
                'may_terminate' => true,
              ),
              'thingskjal' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/thingskjal[/:document_id]',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\DocumentController',
                    'identifier' => 'document_id',
                  ),
                ),
                'may_terminate' => true,
                'child_routes' => 
                array (
                  'flutningsmenn' => 
                  array (
                    'type' => 'Zend\\Router\\Http\\Segment',
                    'options' => 
                    array (
                      'route' => '/flutningsmenn[/:congressman_id]',
                      'defaults' => 
                      array (
                        'controller' => 'Althingi\\Controller\\CongressmanDocumentController',
                        'identifier' => 'congressman_id',
                      ),
                    ),
                  ),
                ),
              ),
              'atkvaedagreidslur' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/atkvaedagreidslur[/:vote_id]',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\VoteController',
                    'identifier' => 'vote_id',
                  ),
                ),
                'may_terminate' => true,
                'child_routes' => 
                array (
                  'atkvaedagreidsla' => 
                  array (
                    'type' => 'Zend\\Router\\Http\\Segment',
                    'options' => 
                    array (
                      'route' => '/atkvaedi[/:vote_item_id]',
                      'defaults' => 
                      array (
                        'controller' => 'Althingi\\Controller\\VoteItemController',
                        'identifier' => 'vote_item_id',
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
          'nefndir' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/nefndir[/:committee_id]',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\AssemblyCommitteeController',
                'identifier' => 'committee_id',
              ),
            ),
            'may_terminate' => true,
            'child_routes' => 
            array (
              'nefndarfundir' => 
              array (
                'type' => 'Zend\\Router\\Http\\Segment',
                'options' => 
                array (
                  'route' => '/nefndarfundir[/:committee_meeting_id]',
                  'defaults' => 
                  array (
                    'controller' => 'Althingi\\Controller\\CommitteeMeetingController',
                    'identifier' => 'committee_meeting_id',
                  ),
                ),
                'may_terminate' => true,
                'child_routes' => 
                array (
                  'dagskralidir' => 
                  array (
                    'type' => 'Zend\\Router\\Http\\Segment',
                    'options' => 
                    array (
                      'route' => '/dagskralidir[/:committee_meeting_agenda_id]',
                      'defaults' => 
                      array (
                        'controller' => 'Althingi\\Controller\\CommitteeMeetingAgendaController',
                        'identifier' => 'committee_meeting_agenda_id',
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
          'efnisflokkar' => 
          array (
            'type' => 'Zend\\Router\\Http\\Literal',
            'options' => 
            array (
              'route' => '/efnisflokkar',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\CategoryController',
                'action' => 'assembly-summary',
              ),
            ),
          ),
        ),
      ),
      'nefndir' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/nefndir[/:committee_id]',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\CommitteeController',
            'identifier' => 'committee_id',
          ),
        ),
      ),
      'thingmenn' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/thingmenn[/:congressman_id]',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\CongressmanController',
            'identifier' => 'congressman_id',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'thingmal' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/thingmal[/:issue_id]',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\CongressmanIssueController',
                'identifier' => 'issue_id',
              ),
            ),
          ),
          'thingseta' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/thingseta[/:session_id]',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\SessionController',
                'identifier' => 'session_id',
              ),
            ),
            'may_terminate' => true,
          ),
        ),
      ),
      'thingflokkar' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/thingflokkar[/:id]',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\PartyController',
          ),
        ),
      ),
      'kjordaemi' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/kjordaemi[/:id]',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\ConstituencyController',
          ),
        ),
      ),
      'forsetar' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/forsetar[/:id]',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\PresidentController',
          ),
        ),
      ),
      'efnisflokkar' => 
      array (
        'type' => 'Zend\\Router\\Http\\Segment',
        'options' => 
        array (
          'route' => '/thingmal/efnisflokkar[/:super_category_id]',
          'defaults' => 
          array (
            'controller' => 'Althingi\\Controller\\SuperCategoryController',
            'identifier' => 'super_category_id',
          ),
        ),
        'may_terminate' => true,
        'child_routes' => 
        array (
          'undirflokkar' => 
          array (
            'type' => 'Zend\\Router\\Http\\Segment',
            'options' => 
            array (
              'route' => '/undirflokkar[/:category_id]',
              'defaults' => 
              array (
                'controller' => 'Althingi\\Controller\\CategoryController',
                'identifier' => 'category_id',
              ),
            ),
          ),
        ),
      ),
    ),
  ),
  'controller_plugins' => 
  array (
    'aliases' => 
    array (
      'CreateConsoleNotFoundModel' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
      'createConsoleNotFoundModel' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
      'createconsolenotfoundmodel' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
      'Zend\\Mvc\\Controller\\Plugin\\CreateConsoleNotFoundModel::class' => 'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel',
    ),
    'factories' => 
    array (
      'Zend\\Mvc\\Console\\Controller\\Plugin\\CreateConsoleNotFoundModel' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
    ),
  ),
  'console' => 
  array (
    'router' => 
    array (
      'routes' => 
      array (
        'speech' => 
        array (
          'options' => 
          array (
            'route' => 'index:speech',
            'defaults' => 
            array (
              'controller' => 'Althingi\\Controller\\Console\\SearchIndexerController',
              'action' => 'speech',
            ),
          ),
        ),
        'issue' => 
        array (
          'options' => 
          array (
            'route' => 'index:issue',
            'defaults' => 
            array (
              'controller' => 'Althingi\\Controller\\Console\\SearchIndexerController',
              'action' => 'issue',
            ),
          ),
        ),
        'status' => 
        array (
          'options' => 
          array (
            'route' => 'index:status [--assembly=|-a] [--type=|-t]',
            'defaults' => 
            array (
              'controller' => 'Althingi\\Controller\\Console\\IssueStatusController',
              'action' => 'index',
            ),
          ),
        ),
        'status-list' => 
        array (
          'options' => 
          array (
            'route' => 'index:status-list',
            'defaults' => 
            array (
              'controller' => 'Althingi\\Controller\\Console\\IssueStatusController',
              'action' => 'status-list',
            ),
          ),
        ),
        'document' => 
        array (
          'options' => 
          array (
            'route' => 'document:api',
            'defaults' => 
            array (
              'controller' => 'Althingi\\Controller\\Console\\DocumentApiController',
              'action' => 'index',
            ),
          ),
        ),
      ),
    ),
  ),
  'view_helpers' => 
  array (
    'aliases' => 
    array (
      'form' => 'Zend\\Form\\View\\Helper\\Form',
      'Form' => 'Zend\\Form\\View\\Helper\\Form',
      'formbutton' => 'Zend\\Form\\View\\Helper\\FormButton',
      'form_button' => 'Zend\\Form\\View\\Helper\\FormButton',
      'formButton' => 'Zend\\Form\\View\\Helper\\FormButton',
      'FormButton' => 'Zend\\Form\\View\\Helper\\FormButton',
      'formcaptcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'form_captcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'formCaptcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'FormCaptcha' => 'Zend\\Form\\View\\Helper\\FormCaptcha',
      'captchadumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captcha_dumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captcha/dumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'CaptchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'formcaptchadumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'form_captcha_dumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'formCaptchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'FormCaptchaDumb' => 'Zend\\Form\\View\\Helper\\Captcha\\Dumb',
      'captchafiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captcha/figlet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captcha_figlet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'CaptchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'formcaptchafiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'form_captcha_figlet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'formCaptchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'FormCaptchaFiglet' => 'Zend\\Form\\View\\Helper\\Captcha\\Figlet',
      'captchaimage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captcha/image' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captcha_image' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'CaptchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'formcaptchaimage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'form_captcha_image' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'formCaptchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'FormCaptchaImage' => 'Zend\\Form\\View\\Helper\\Captcha\\Image',
      'captcharecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'captcha/recaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'captcha_recaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'captchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'CaptchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'formcaptcharecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'form_captcha_recaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'formCaptchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'FormCaptchaRecaptcha' => 'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha',
      'formcheckbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'form_checkbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'formCheckbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'FormCheckbox' => 'Zend\\Form\\View\\Helper\\FormCheckbox',
      'formcollection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'form_collection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'formCollection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'FormCollection' => 'Zend\\Form\\View\\Helper\\FormCollection',
      'formcolor' => 'Zend\\Form\\View\\Helper\\FormColor',
      'form_color' => 'Zend\\Form\\View\\Helper\\FormColor',
      'formColor' => 'Zend\\Form\\View\\Helper\\FormColor',
      'FormColor' => 'Zend\\Form\\View\\Helper\\FormColor',
      'formdate' => 'Zend\\Form\\View\\Helper\\FormDate',
      'form_date' => 'Zend\\Form\\View\\Helper\\FormDate',
      'formDate' => 'Zend\\Form\\View\\Helper\\FormDate',
      'FormDate' => 'Zend\\Form\\View\\Helper\\FormDate',
      'formdatetime' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'form_date_time' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'formDateTime' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'FormDateTime' => 'Zend\\Form\\View\\Helper\\FormDateTime',
      'formdatetimelocal' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'form_date_time_local' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'formDateTimeLocal' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'FormDateTimeLocal' => 'Zend\\Form\\View\\Helper\\FormDateTimeLocal',
      'formdatetimeselect' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'form_date_time_select' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'formDateTimeSelect' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'FormDateTimeSelect' => 'Zend\\Form\\View\\Helper\\FormDateTimeSelect',
      'formdateselect' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'form_date_select' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'formDateSelect' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'FormDateSelect' => 'Zend\\Form\\View\\Helper\\FormDateSelect',
      'form_element' => 'Zend\\Form\\View\\Helper\\FormElement',
      'formelement' => 'Zend\\Form\\View\\Helper\\FormElement',
      'formElement' => 'Zend\\Form\\View\\Helper\\FormElement',
      'FormElement' => 'Zend\\Form\\View\\Helper\\FormElement',
      'form_element_errors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'formelementerrors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'formElementErrors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'FormElementErrors' => 'Zend\\Form\\View\\Helper\\FormElementErrors',
      'form_email' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'formemail' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'formEmail' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'FormEmail' => 'Zend\\Form\\View\\Helper\\FormEmail',
      'form_file' => 'Zend\\Form\\View\\Helper\\FormFile',
      'formfile' => 'Zend\\Form\\View\\Helper\\FormFile',
      'formFile' => 'Zend\\Form\\View\\Helper\\FormFile',
      'FormFile' => 'Zend\\Form\\View\\Helper\\FormFile',
      'formfileapcprogress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'form_file_apc_progress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'formFileApcProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'FormFileApcProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress',
      'formfilesessionprogress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'form_file_session_progress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'formFileSessionProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'FormFileSessionProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress',
      'formfileuploadprogress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'form_file_upload_progress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'formFileUploadProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'FormFileUploadProgress' => 'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress',
      'formhidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'form_hidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'formHidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'FormHidden' => 'Zend\\Form\\View\\Helper\\FormHidden',
      'formimage' => 'Zend\\Form\\View\\Helper\\FormImage',
      'form_image' => 'Zend\\Form\\View\\Helper\\FormImage',
      'formImage' => 'Zend\\Form\\View\\Helper\\FormImage',
      'FormImage' => 'Zend\\Form\\View\\Helper\\FormImage',
      'forminput' => 'Zend\\Form\\View\\Helper\\FormInput',
      'form_input' => 'Zend\\Form\\View\\Helper\\FormInput',
      'formInput' => 'Zend\\Form\\View\\Helper\\FormInput',
      'FormInput' => 'Zend\\Form\\View\\Helper\\FormInput',
      'formlabel' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'form_label' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'formLabel' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'FormLabel' => 'Zend\\Form\\View\\Helper\\FormLabel',
      'formmonth' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'form_month' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'formMonth' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'FormMonth' => 'Zend\\Form\\View\\Helper\\FormMonth',
      'formmonthselect' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'form_month_select' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'formMonthSelect' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'FormMonthSelect' => 'Zend\\Form\\View\\Helper\\FormMonthSelect',
      'formmulticheckbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'form_multi_checkbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'formMultiCheckbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'FormMultiCheckbox' => 'Zend\\Form\\View\\Helper\\FormMultiCheckbox',
      'formnumber' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'form_number' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'formNumber' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'FormNumber' => 'Zend\\Form\\View\\Helper\\FormNumber',
      'formpassword' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'form_password' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'formPassword' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'FormPassword' => 'Zend\\Form\\View\\Helper\\FormPassword',
      'formradio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'form_radio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'formRadio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'FormRadio' => 'Zend\\Form\\View\\Helper\\FormRadio',
      'formrange' => 'Zend\\Form\\View\\Helper\\FormRange',
      'form_range' => 'Zend\\Form\\View\\Helper\\FormRange',
      'formRange' => 'Zend\\Form\\View\\Helper\\FormRange',
      'FormRange' => 'Zend\\Form\\View\\Helper\\FormRange',
      'formreset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'form_reset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'formReset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'FormReset' => 'Zend\\Form\\View\\Helper\\FormReset',
      'formrow' => 'Zend\\Form\\View\\Helper\\FormRow',
      'form_row' => 'Zend\\Form\\View\\Helper\\FormRow',
      'formRow' => 'Zend\\Form\\View\\Helper\\FormRow',
      'FormRow' => 'Zend\\Form\\View\\Helper\\FormRow',
      'formsearch' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'form_search' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'formSearch' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'FormSearch' => 'Zend\\Form\\View\\Helper\\FormSearch',
      'formselect' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'form_select' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'formSelect' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'FormSelect' => 'Zend\\Form\\View\\Helper\\FormSelect',
      'formsubmit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'form_submit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'formSubmit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'FormSubmit' => 'Zend\\Form\\View\\Helper\\FormSubmit',
      'formtel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'form_tel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'formTel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'FormTel' => 'Zend\\Form\\View\\Helper\\FormTel',
      'formtext' => 'Zend\\Form\\View\\Helper\\FormText',
      'form_text' => 'Zend\\Form\\View\\Helper\\FormText',
      'formText' => 'Zend\\Form\\View\\Helper\\FormText',
      'FormText' => 'Zend\\Form\\View\\Helper\\FormText',
      'formtextarea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'form_text_area' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'formTextarea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'formTextArea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'FormTextArea' => 'Zend\\Form\\View\\Helper\\FormTextarea',
      'formtime' => 'Zend\\Form\\View\\Helper\\FormTime',
      'form_time' => 'Zend\\Form\\View\\Helper\\FormTime',
      'formTime' => 'Zend\\Form\\View\\Helper\\FormTime',
      'FormTime' => 'Zend\\Form\\View\\Helper\\FormTime',
      'formurl' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'form_url' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'formUrl' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'FormUrl' => 'Zend\\Form\\View\\Helper\\FormUrl',
      'formweek' => 'Zend\\Form\\View\\Helper\\FormWeek',
      'form_week' => 'Zend\\Form\\View\\Helper\\FormWeek',
      'formWeek' => 'Zend\\Form\\View\\Helper\\FormWeek',
      'FormWeek' => 'Zend\\Form\\View\\Helper\\FormWeek',
    ),
    'factories' => 
    array (
      'Zend\\Form\\View\\Helper\\Form' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormButton' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormCaptcha' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\Dumb' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\Figlet' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\Image' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\Captcha\\ReCaptcha' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormCheckbox' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormCollection' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormColor' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDate' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateTime' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateTimeLocal' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateTimeSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormDateSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormElement' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormElementErrors' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormEmail' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormFile' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\File\\FormFileApcProgress' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\File\\FormFileSessionProgress' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\File\\FormFileUploadProgress' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormHidden' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormImage' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormInput' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormLabel' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormMonth' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormMonthSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormMultiCheckbox' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormNumber' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormPassword' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormRadio' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormRange' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormReset' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormRow' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormSearch' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormSelect' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormSubmit' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormTel' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormText' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormTextarea' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormTime' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormUrl' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
      'Zend\\Form\\View\\Helper\\FormWeek' => 'Zend\\ServiceManager\\Factory\\InvokableFactory',
    ),
  ),
  'controllers' => 
  array (
    'factories' => 
    array (
      'Althingi\\Controller\\IndexController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\AssemblyController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CongressmanController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\SessionController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CongressmanSessionController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\PartyController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\ConstituencyController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\PlenaryController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\IssueController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\UndocumentedIssueController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\SpeechController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\VoteController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\VoteItemController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CongressmanIssueController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CongressmanDocumentController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\DocumentController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CommitteeController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CabinetController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\PresidentController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\PresidentAssemblyController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\SuperCategoryController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CategoryController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\IssueCategoryController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CommitteeMeetingController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\CommitteeMeetingAgendaController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\AssemblyCommitteeController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\HighlightController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\Console\\SearchIndexerController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\Console\\DocumentApiController' => 
      Closure::__set_state(array(
      )),
      'Althingi\\Controller\\Console\\IssueStatusController' => 
      Closure::__set_state(array(
      )),
    ),
  ),
  'view_manager' => 
  array (
    'display_not_found_reason' => true,
    'display_exceptions' => true,
    'doctype' => 'HTML5',
    'not_found_template' => 'error/404',
    'exception_template' => 'error/index',
    'template_map' => 
    array (
      'layout/layout' => '/Users/einar.adalsteinsson/workspace/Althingi/module/Althingi/config/../view/layout/layout.phtml',
      'application/index/index' => '/Users/einar.adalsteinsson/workspace/Althingi/module/Althingi/config/../view/application/index/index.phtml',
      'error/404' => '/Users/einar.adalsteinsson/workspace/Althingi/module/Althingi/config/../view/error/404.phtml',
      'error/index' => '/Users/einar.adalsteinsson/workspace/Althingi/module/Althingi/config/../view/error/index.phtml',
    ),
    'template_path_stack' => 
    array (
      0 => '/Users/einar.adalsteinsson/workspace/Althingi/module/Althingi/config/../view',
    ),
    'strategies' => 
    array (
      0 => 'MessageStrategy',
    ),
  ),
);