CREATE TABLE [dbo].[US_WEB_COMAGIC_AC](
	[US_WEB_COMAGIC_AC_ID] [int] IDENTITY(1,1) NOT NULL,
	[ID] [int] NULL,
	[NAME] [varchar](250) NULL,
 CONSTRAINT [PK_US_WEB_COMAGIC_AC] PRIMARY KEY CLUSTERED 
(
	[US_WEB_COMAGIC_AC_ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_COMAGIC_AC_TMP](
	[ID] [int] NULL,
	[NAME] [varchar](250) NULL
) ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_COMAGIC_CALLS](
	[US_WEB_COMAGIC_CALLS_ID] [int] IDENTITY(1,1) NOT NULL,
	[MEDIALOG_CALL_ID] [int] NULL,
	[id] [int] NULL,
	[call_date] [varchar](50) NULL,
	[session_start] [varchar](50) NULL,
	[communication_type] [varchar](50) NULL,
	[status] [varchar](50) NULL,
	[numa] [varchar](50) NULL,
	[numb] [varchar](50) NULL,
	[wait_time] [int] NULL,
	[duration] [int] NULL,
	[file_link] [text] NULL,
	[operator_name] [varchar](250) NULL,
	[coach_name] [varchar](250) NULL,
	[scenario_name] [varchar](250) NULL,
	[is_transfer] [varchar](50) NULL,
	[tags] [text] NULL,
	[communication_number] [int] NULL,
	[site_id] [int] NULL,
	[ac_id] [int] NULL,
	[visitor_id] [varchar](50) NULL,
	[visitor_type] [varchar](50) NULL,
	[visits_count] [int] NULL,
	[other_adv_contacts] [varchar](50) NULL,
	[country] [varchar](250) NULL,
	[region] [varchar](250) NULL,
	[city] [varchar](50) NULL,
	[visitor_first_ac] [int] NULL,
	[search_engine] [varchar](250) NULL,
	[search_query] [text] NULL,
	[page_url] [text] NULL,
	[referrer_domain] [text] NULL,
	[referrer] [text] NULL,
	[ua_client_id] [text] NULL,
	[utm_campaign] [text] NULL,
	[utm_content] [text] NULL,
	[utm_medium] [text] NULL,
	[utm_source] [text] NULL,
	[utm_term] [text] NULL,
	[os_ad_id] [text] NULL,
	[os_campaign_id] [text] NULL,
	[os_service_name] [text] NULL,
	[os_source_id] [text] NULL,
	[gclid] [text] NULL,
	[yclid] [text] NULL,
	[ef_id] [text] NULL,
	[session_id] [varchar](50) NULL,
	[sale_date] [varchar](50) NULL,
	[sale_cost] [varchar](50) NULL,
	[direction] [varchar](50) NULL,
	[last_query] [text] NULL,
	[is_visitor_by_numa] [varchar](50) NULL,
 CONSTRAINT [PK_US_WEB_COMAGIC_CALLS] PRIMARY KEY CLUSTERED 
(
	[US_WEB_COMAGIC_CALLS_ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON, FILLFACTOR = 90) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_COMAGIC_SITE](
	[ID] [int] NOT NULL,
	[domain] [varchar](250) NULL
) ON [PRIMARY]

ALTER TABLE [dbo].[US_WEB_COMAGIC_SITE] ADD  CONSTRAINT [PK_US_WEB_COMAGIC_SITE] PRIMARY KEY CLUSTERED 
(
	[ID] ASC
)WITH (PAD_INDEX  = OFF, STATISTICS_NORECOMPUTE  = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS  = ON, ALLOW_PAGE_LOCKS  = ON) ON [PRIMARY]

GO

CREATE TABLE [dbo].[US_WEB_COMAGIC_SITE_TMP](
	[ID] [int] NULL,
	[domain] [varchar](250) NULL
) ON [PRIMARY]
