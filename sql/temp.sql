select e.Project, e.Team, e.Points
from
(
	select p.id as Project, t.id as Team, sum(s.points) as Points
	from mzpc_project as p, mzpc_score as s join mzpc_team as t on s.team_id = t.id
	where t.project_id = p.id
	group by t.id
) as e
group by e.Points desc
limit 3

-- simpler (project specifics) top 3 query

((((((((((((((((((((((((((((((((((

select t.name, max(s.points) as points
from mzpc_team as t join mzpc_score as s on t.id = s.team_id,
(
	select sum(s.points)
    from mzpc_project as p, mzpc_team as t join mzpc_score as s on t.id = s.team_id
    where t.project_id = p.id
    group by t.name
    
) as e
group by t.name

-- it's gross, it's ugly, it's redundant, but boy does it return team names and rank them by max sum of points

------------------------------

select t.project_id as Project, t.id as Team, s.points as Points
from mzpc_team as t join mzpc_score as s on t.id = s.team_id,
(
	select sum(s.points)
	from mzpc_project as p, mzpc_team as t join mzpc_score as s on t.id = s.team_id
	where t.project_id = p.id
	group by t.project_id
) as e
group by s.points desc

----------------------------------

select e.Project, e.Team, e.Points
from
(
	select p.id as Project, t.id as Team, sum(s.points) as Points
	from mzpc_project as p, mzpc_score as s join mzpc_team as t on s.team_id = t.id
	where t.project_id = p.id
	group by t.id
) as e
group by e.Points desc
limit 3

+++++++++++++++++++++++++++++++++++++++++++++++++

select e1.Project, e1.Team, e1.Points
from(
	select e2.Project, e2.Team, e2.Points
	from(
		select p.id as Project, t.id as Team, sum(s.points) as Points
		from mzpc_project as p, mzpc_score as s join mzpc_team as t on s.team_id = t.id
		where t.project_id = p.id
		group by t.id
	) as e2
	group by e2.Points desc
	limit 3
) as e1
order by e1.Project

////////////////////////////
