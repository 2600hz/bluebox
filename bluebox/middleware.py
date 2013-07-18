import re

# Logger creation
import logging
log = logging.getLogger(__name__)

from lxml import etree

from django.conf import settings

class MiddlewareBluebox:
    def process_request(self, request):
        search = re.search('account/(?P<account_id>[a-f0-9]{8}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{4}-[a-f0-9]{12})', request.path)

        if search:
            tree = etree.parse("%s/conf/freeswitch.xml" % settings.FREESWITCH_PATH)
            
            # Directory
            directory = tree.find('//section[@name="directory"]/X-PRE-PROCESS')
            directory.set('data', '%s%s/directory.xml' % (settings.BLUEBOX_CONFIG_PATH, search.group('account_id')))
            
            # Dialplan
            directory = tree.find('//section[@name="dialplan"]/X-PRE-PROCESS')
            directory.set('data', '%s%s/dialplan.xml' % (settings.BLUEBOX_CONFIG_PATH, search.group('account_id')))

            tree.write("%s/conf/freeswitch.xml" % settings.FREESWITCH_PATH)

        return None