import os

# ==============================================================================
# project settings
# ==============================================================================

ADMINS = (
    # ('Your Name', 'your_email@domain.com'),
)

MANAGERS = ADMINS

# ==============================================================================
# debug settings
# ==============================================================================

DEBUG = True
TEMPLATE_DEBUG = DEBUG

# ==============================================================================
# auth settings
# ==============================================================================

LOGIN_URL = '/player/login/'
LOGOUT_URL = '/player/logout/'
LOGIN_REDIRECT_URL = '/'

# ==============================================================================
# database settings
# ==============================================================================

DATABASE_ENGINE = 'mysql'
DATABASE_NAME = 'masterserver'
DATABASE_USER = 'masterserver'
DATABASE_PASSWORD = 'masterserver'
DATABASE_HOST = ''
DATABASE_PORT = ''

# ==============================================================================
# i18n and url settings
# ==============================================================================

TIME_ZONE = 'America/Chicago'
LANGUAGE_CODE = 'en-us'

USE_I18N = True
SITE_ID = 1

MEDIA_ROOT = os.path.join(os.path.dirname(__file__), '../media'),
MEDIA_URL = '/media/'
ADMIN_MEDIA_PREFIX = '/django_admin_media/'

ROOT_URLCONF = 's2site.urls'

# ==============================================================================
# application and middleware settings
# ==============================================================================

INSTALLED_APPS = (
    'django.contrib.auth',
    'django.contrib.contenttypes',
    'django.contrib.sessions',
    'django.contrib.sites',
    'django.contrib.admin',
    'lib',
    's2site.player',
    's2site.server'
)

MIDDLEWARE_CLASSES = (
    'django.middleware.common.CommonMiddleware',
    'django.contrib.sessions.middleware.SessionMiddleware',
    'django.contrib.auth.middleware.AuthenticationMiddleware',
)

TEMPLATE_CONTEXT_PROCESSORS = (
    'django.core.context_processors.auth',
    'django.core.context_processors.debug',
    'django.core.context_processors.i18n',
    'django.core.context_processors.media',
    'django.core.context_processors.request',
)

TEMPLATE_LOADERS = (
    'django.template.loaders.filesystem.load_template_source',
    'django.template.loaders.app_directories.load_template_source',
)

TEMPLATE_DIRS = (
	os.path.join(os.path.dirname(__file__), '../template'),
)

# ==============================================================================
# the secret key
# ==============================================================================

SECRET_KEY = 'f)r)+#w$&a9g2_-$wbzo1258dgqgz6afr!$dy&_6wqpe=!lxpp'