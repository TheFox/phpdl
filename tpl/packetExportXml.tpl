<?xml version="1.0" encoding="UTF-8" ?>
<fox21at>
	<head>
		<title>{$siteName}</title>
		<url>http://fox21.at</url>
		<urlFull><![CDATA[{$phpFullURI}]]></urlFull>
		<description>{$siteKeywords}</description>
		<copyright>{$siteCopyright}</copyright>
		<date>
			<country>AT</country>
			<format>{$dateFormatLong}</format>
			<ctime>{$dateLong}</ctime>
			<rfc2822>{$dateRFC2822}</rfc2822>
		</date>
		<cache on="{$smartyCache}" lifeTime="{$smartyCacheLifetime}" nextUpdate="{$dateNextupdate}" id="{$cacheId}" />
	</head>
	<content>
		<dlfileErrors>
			{$dlfileErrors}
		</dlfileErrors>
		<packets>
			<packet id="{$packetId}">
				<name>{$packetName}</name>
				<source><![CDATA[{$packetSource}]]></source>
				<password>{$packetPassword}</password>
				<md5Verified>{$packetMd5Verified}</md5Verified>
				<ctime>{$packetCtime}</ctime>
				<stime>{$packetStime}</stime>
				<ftime>{$packetFtime}</ftime>
				<files>
					{$files}
				</files>
			</packet>
		</packets>
	</content>
</fox21at>
