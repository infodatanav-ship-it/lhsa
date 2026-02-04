SELECT dg.id, dg.document_id, dg.group_id, gs.name FROM `document_groups` dg
INNER JOIN groups gs ON gs.id = dg.group_id;