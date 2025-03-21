USE [master]
GO
/****** Object:  Database [checksheet]    Script Date: 21/03/2025 08.41.59 ******/
CREATE DATABASE [checksheet]
 CONTAINMENT = NONE
 ON  PRIMARY 
( NAME = N'checksheet', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL16.MSSQLSERVER\MSSQL\DATA\checksheet.mdf' , SIZE = 8192KB , MAXSIZE = UNLIMITED, FILEGROWTH = 65536KB )
 LOG ON 
( NAME = N'checksheet_log', FILENAME = N'C:\Program Files\Microsoft SQL Server\MSSQL16.MSSQLSERVER\MSSQL\DATA\checksheet_log.ldf' , SIZE = 8192KB , MAXSIZE = 2048GB , FILEGROWTH = 65536KB )
 WITH CATALOG_COLLATION = DATABASE_DEFAULT, LEDGER = OFF
GO
ALTER DATABASE [checksheet] SET COMPATIBILITY_LEVEL = 160
GO
IF (1 = FULLTEXTSERVICEPROPERTY('IsFullTextInstalled'))
begin
EXEC [checksheet].[dbo].[sp_fulltext_database] @action = 'enable'
end
GO
ALTER DATABASE [checksheet] SET ANSI_NULL_DEFAULT OFF 
GO
ALTER DATABASE [checksheet] SET ANSI_NULLS OFF 
GO
ALTER DATABASE [checksheet] SET ANSI_PADDING OFF 
GO
ALTER DATABASE [checksheet] SET ANSI_WARNINGS OFF 
GO
ALTER DATABASE [checksheet] SET ARITHABORT OFF 
GO
ALTER DATABASE [checksheet] SET AUTO_CLOSE OFF 
GO
ALTER DATABASE [checksheet] SET AUTO_SHRINK OFF 
GO
ALTER DATABASE [checksheet] SET AUTO_UPDATE_STATISTICS ON 
GO
ALTER DATABASE [checksheet] SET CURSOR_CLOSE_ON_COMMIT OFF 
GO
ALTER DATABASE [checksheet] SET CURSOR_DEFAULT  GLOBAL 
GO
ALTER DATABASE [checksheet] SET CONCAT_NULL_YIELDS_NULL OFF 
GO
ALTER DATABASE [checksheet] SET NUMERIC_ROUNDABORT OFF 
GO
ALTER DATABASE [checksheet] SET QUOTED_IDENTIFIER OFF 
GO
ALTER DATABASE [checksheet] SET RECURSIVE_TRIGGERS OFF 
GO
ALTER DATABASE [checksheet] SET  DISABLE_BROKER 
GO
ALTER DATABASE [checksheet] SET AUTO_UPDATE_STATISTICS_ASYNC OFF 
GO
ALTER DATABASE [checksheet] SET DATE_CORRELATION_OPTIMIZATION OFF 
GO
ALTER DATABASE [checksheet] SET TRUSTWORTHY OFF 
GO
ALTER DATABASE [checksheet] SET ALLOW_SNAPSHOT_ISOLATION OFF 
GO
ALTER DATABASE [checksheet] SET PARAMETERIZATION SIMPLE 
GO
ALTER DATABASE [checksheet] SET READ_COMMITTED_SNAPSHOT OFF 
GO
ALTER DATABASE [checksheet] SET HONOR_BROKER_PRIORITY OFF 
GO
ALTER DATABASE [checksheet] SET RECOVERY FULL 
GO
ALTER DATABASE [checksheet] SET  MULTI_USER 
GO
ALTER DATABASE [checksheet] SET PAGE_VERIFY CHECKSUM  
GO
ALTER DATABASE [checksheet] SET DB_CHAINING OFF 
GO
ALTER DATABASE [checksheet] SET FILESTREAM( NON_TRANSACTED_ACCESS = OFF ) 
GO
ALTER DATABASE [checksheet] SET TARGET_RECOVERY_TIME = 60 SECONDS 
GO
ALTER DATABASE [checksheet] SET DELAYED_DURABILITY = DISABLED 
GO
ALTER DATABASE [checksheet] SET ACCELERATED_DATABASE_RECOVERY = OFF  
GO
EXEC sys.sp_db_vardecimal_storage_format N'checksheet', N'ON'
GO
ALTER DATABASE [checksheet] SET QUERY_STORE = ON
GO
ALTER DATABASE [checksheet] SET QUERY_STORE (OPERATION_MODE = READ_WRITE, CLEANUP_POLICY = (STALE_QUERY_THRESHOLD_DAYS = 30), DATA_FLUSH_INTERVAL_SECONDS = 900, INTERVAL_LENGTH_MINUTES = 60, MAX_STORAGE_SIZE_MB = 1000, QUERY_CAPTURE_MODE = AUTO, SIZE_BASED_CLEANUP_MODE = AUTO, MAX_PLANS_PER_QUERY = 200, WAIT_STATS_CAPTURE_MODE = ON)
GO
USE [checksheet]
GO
/****** Object:  Table [dbo].[migrations]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[migrations](
	[id] [bigint] IDENTITY(1,1) NOT NULL,
	[version] [varchar](255) NOT NULL,
	[class] [varchar](255) NOT NULL,
	[group] [varchar](255) NOT NULL,
	[namespace] [varchar](255) NOT NULL,
	[time] [int] NOT NULL,
	[batch] [int] NOT NULL,
 CONSTRAINT [pk_migrations] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[preuse_tb_checksheet]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[preuse_tb_checksheet](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[bulan] [nvarchar](20) NOT NULL,
	[departemen] [varchar](255) NOT NULL,
	[seksi] [varchar](255) NOT NULL,
	[master_id] [int] NULL,
	[mesin] [varchar](50) NOT NULL,
 CONSTRAINT [PK__tb_check__3213E83FA7D6D621] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[preuse_tb_detail_checksheet]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[preuse_tb_detail_checksheet](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[checksheet_id] [int] NOT NULL,
	[item_check] [varchar](255) NOT NULL,
	[inspeksi] [varchar](255) NOT NULL,
	[standar] [varchar](255) NOT NULL,
	[status] [varchar](50) NOT NULL,
	[created_at] [datetime] NULL,
	[npk] [varchar](50) NOT NULL,
	[tanggal] [date] NULL,
	[kolom] [varchar](50) NULL,
	[is_submitted] [bit] NOT NULL,
	[deleted_at] [datetime] NULL,
 CONSTRAINT [PK__tb_detai__3213E83F28A04140] PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[preuse_tb_detail_master]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[preuse_tb_detail_master](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[master_id] [int] NOT NULL,
	[item_check] [varchar](255) NOT NULL,
	[inspeksi] [varchar](255) NOT NULL,
	[standar] [varchar](255) NOT NULL,
	[created_at] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
/****** Object:  Table [dbo].[preuse_tb_master]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[preuse_tb_master](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[mesin] [nvarchar](max) NULL,
	[created_at] [datetime] NULL,
	[judul_checksheet] [varchar](255) NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[preuse_tb_status_change_log]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[preuse_tb_status_change_log](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[detail_checksheet_id] [int] NOT NULL,
	[previous_status] [varchar](10) NOT NULL,
	[new_status] [varchar](10) NOT NULL,
	[reason] [text] NOT NULL,
	[changed_by] [varchar](50) NOT NULL,
	[changed_at] [datetime] NOT NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]
GO
/****** Object:  Table [dbo].[tb_user]    Script Date: 21/03/2025 08.41.59 ******/
SET ANSI_NULLS ON
GO
SET QUOTED_IDENTIFIER ON
GO
CREATE TABLE [dbo].[tb_user](
	[id] [int] IDENTITY(1,1) NOT NULL,
	[username] [varchar](50) NOT NULL,
	[email] [varchar](100) NOT NULL,
	[password] [varchar](255) NOT NULL,
	[created_at] [datetime] NULL,
PRIMARY KEY CLUSTERED 
(
	[id] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, IGNORE_DUP_KEY = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
) ON [PRIMARY]
GO
SET IDENTITY_INSERT [dbo].[preuse_tb_checksheet] ON 

INSERT [dbo].[preuse_tb_checksheet] ([id], [bulan], [departemen], [seksi], [master_id], [mesin]) VALUES (2, N'2025-03', N'QA', N'Prod. 2', 2, N'COS')
SET IDENTITY_INSERT [dbo].[preuse_tb_checksheet] OFF
GO
SET IDENTITY_INSERT [dbo].[preuse_tb_detail_checksheet] ON 

INSERT [dbo].[preuse_tb_detail_checksheet] ([id], [checksheet_id], [item_check], [inspeksi], [standar], [status], [created_at], [npk], [tanggal], [kolom], [is_submitted], [deleted_at]) VALUES (17, 2, N'Check B', N'Inpeksi B', N'Standar B', N'NG', CAST(N'2025-03-21T08:29:55.570' AS DateTime), N'12345', CAST(N'2025-03-01' AS Date), N'1', 0, CAST(N'2025-03-21T01:30:21.000' AS DateTime))
INSERT [dbo].[preuse_tb_detail_checksheet] ([id], [checksheet_id], [item_check], [inspeksi], [standar], [status], [created_at], [npk], [tanggal], [kolom], [is_submitted], [deleted_at]) VALUES (18, 2, N'Check C', N'Inpeksi C', N'Standar C', N'OK', CAST(N'2025-03-21T08:29:55.583' AS DateTime), N'12345', CAST(N'2025-03-01' AS Date), N'1', 0, NULL)
SET IDENTITY_INSERT [dbo].[preuse_tb_detail_checksheet] OFF
GO
SET IDENTITY_INSERT [dbo].[preuse_tb_detail_master] ON 

INSERT [dbo].[preuse_tb_detail_master] ([id], [master_id], [item_check], [inspeksi], [standar], [created_at]) VALUES (12, 2, N'Check C', N'Inpeksi C', N'Standar C', CAST(N'2025-03-21T08:30:21.950' AS DateTime))
SET IDENTITY_INSERT [dbo].[preuse_tb_detail_master] OFF
GO
SET IDENTITY_INSERT [dbo].[preuse_tb_master] ON 

INSERT [dbo].[preuse_tb_master] ([id], [mesin], [created_at], [judul_checksheet]) VALUES (2, N'["COS"]', CAST(N'2025-03-20T02:41:57.000' AS DateTime), N'Pre-Use')
SET IDENTITY_INSERT [dbo].[preuse_tb_master] OFF
GO
SET ANSI_PADDING ON
GO
/****** Object:  Index [UQ__tb_user__AB6E616406BD0F27]    Script Date: 21/03/2025 08.42.00 ******/
ALTER TABLE [dbo].[tb_user] ADD UNIQUE NONCLUSTERED 
(
	[email] ASC
)WITH (PAD_INDEX = OFF, STATISTICS_NORECOMPUTE = OFF, SORT_IN_TEMPDB = OFF, IGNORE_DUP_KEY = OFF, ONLINE = OFF, ALLOW_ROW_LOCKS = ON, ALLOW_PAGE_LOCKS = ON, OPTIMIZE_FOR_SEQUENTIAL_KEY = OFF) ON [PRIMARY]
GO
ALTER TABLE [dbo].[preuse_tb_checksheet] ADD  CONSTRAINT [DF__tb_checks__mesin__5BE2A6F2]  DEFAULT ((0)) FOR [mesin]
GO
ALTER TABLE [dbo].[preuse_tb_detail_checksheet] ADD  CONSTRAINT [DF__tb_detail__creat__66603565]  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[preuse_tb_detail_checksheet] ADD  DEFAULT ((0)) FOR [is_submitted]
GO
ALTER TABLE [dbo].[preuse_tb_detail_master] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[preuse_tb_master] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[preuse_tb_status_change_log] ADD  DEFAULT (getdate()) FOR [changed_at]
GO
ALTER TABLE [dbo].[tb_user] ADD  DEFAULT (getdate()) FOR [created_at]
GO
ALTER TABLE [dbo].[preuse_tb_checksheet]  WITH CHECK ADD  CONSTRAINT [fk_master] FOREIGN KEY([master_id])
REFERENCES [dbo].[preuse_tb_master] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[preuse_tb_checksheet] CHECK CONSTRAINT [fk_master]
GO
ALTER TABLE [dbo].[preuse_tb_detail_checksheet]  WITH CHECK ADD  CONSTRAINT [FK__tb_detail__check__6754599E] FOREIGN KEY([checksheet_id])
REFERENCES [dbo].[preuse_tb_checksheet] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[preuse_tb_detail_checksheet] CHECK CONSTRAINT [FK__tb_detail__check__6754599E]
GO
ALTER TABLE [dbo].[preuse_tb_detail_master]  WITH CHECK ADD FOREIGN KEY([master_id])
REFERENCES [dbo].[preuse_tb_master] ([id])
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[preuse_tb_status_change_log]  WITH CHECK ADD  CONSTRAINT [FK_StatusChange_DetailChecksheet] FOREIGN KEY([detail_checksheet_id])
REFERENCES [dbo].[preuse_tb_detail_checksheet] ([id])
ON UPDATE CASCADE
ON DELETE CASCADE
GO
ALTER TABLE [dbo].[preuse_tb_status_change_log] CHECK CONSTRAINT [FK_StatusChange_DetailChecksheet]
GO
USE [master]
GO
ALTER DATABASE [checksheet] SET  READ_WRITE 
GO
