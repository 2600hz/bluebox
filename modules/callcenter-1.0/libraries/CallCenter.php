<?php defined('SYSPATH') or die('No direct access allowed.');

class CallCenter extends API
{
    protected static function tiers_GET($id, $envelope)
    {
        $tiers = self::generalAPI_GET($id, 'tier_id', 'Tier', $envelope);
        
        if($tiers == NULL)
        {
            return array();
        }

        if(is_null($id))
        {
            foreach($tiers as $tier)
            {
                $response[] = self::injectTierAgents($tier);
            }
        }
        else
        {
            // At this point $tiers is actually only one tier
            $response = self::injectTierAgents($tiers);
        }

        return $response;
    }

    // $tier is expected to be an array
    private static function injectTierAgents($tier)
    {
        $tier_agents = Doctrine_Query::create()
                       ->select('*')
                       ->from('TierAgent')
                       ->where('tier_id = ?', arr::get($tier, 'tier_id'))
                       ->orderBy('position')
                       ->execute(array(), Doctrine::HYDRATE_ARRAY);

        $tier['agents'] = $tier_agents;

        return $tier;
    }

    protected static function tiers_POST($id, $envelope)
    {
        if(is_null($id))
        {
            self::throwErrorAndDie('Invalid request', array($id), 410);
        }

        $data = self::requireData($envelope);

        $tier_agents = array();

        if(($agents = arr::get($data, 'agents')))
        {
            foreach($agents as $agent)
            {
                if(($tier_agent_id = arr::get($agent, 'tier_agent_id')))
                {
                    $tier_agent = Doctrine::getTable('TierAgent')->findOneBy('tier_agent_id', $tier_agent_id);
                }
                else
                {
                    $tier_agent = new TierAgent();
                }

                try
                {
                    $tier_agent->synchronizeWithArray($agent);
                    $tier_agent->save();

                    $tier_agents[] = $tier_agent->toArray();
                }
                catch(Exception $e)
                {
                    self::throwErrorAndDie('Invalid data', Bluebox_Controller::$validation->errors(), 400);
                }
            }

            arr::remove('agents', $data);

            arr::merge($envelope['data'], $data);
        }

        $response = self::generalAPI_POST($id, 'tier_id', 'Tier', $envelope);

        $response['agents'] = $tier_agents;

        return $response;
    }
}