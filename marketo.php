<?php
public function get_lead_by($type, $value)
	{
		$lead = new stdClass;
		$lead->leadKey = new stdClass;
		$lead->leadKey->keyType  = strtoupper($type);
		$lead->leadKey->keyValue = $value;
		try 
		{
			$result = $this->request('getLead', $lead);
			$leads = $this->format_leads($result);
		}
		catch (Exception $e) 
		{
			if (isset($e->detail->serviceException->code) && $e->detail->serviceException->code == '20103') 
			{
				// No leads were found
				$leads = FALSE;
			}
			else
			{
				throw new Exception($e, 1);
			}
		}
		
		return $leads;
	}
?>