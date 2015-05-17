### Drupal migration

```sql
SELECT 
*
FROM (
SELECT 
node.nid,
node_revisions.vid,
node_revisions.timestamp,
node.title,
node_revisions.body,
node.created,
node.changed,
node.comment,
    node.status
FROM `node` 
INNER JOIN node_revisions
ON node.nid = node_revisions.nid
WHERE 1
ORDER BY node_revisions.vid DESC
    ) as `n` GROUP BY n.nid
ORDER BY n.nid DESC
```
