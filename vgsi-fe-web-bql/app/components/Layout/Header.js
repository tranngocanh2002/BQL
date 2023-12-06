import React, { PureComponent, Fragment } from "react";
import PropTypes, { func } from "prop-types";
import { Menu, Icon, Layout, Avatar, Typography, Row, Col } from "antd";
import { Ellipsis, NoticeIcon } from "ant-design-pro";
import { injectIntl } from "react-intl";
import messages from "./messages";
import moment from "moment";
import classnames from "classnames";
import styles from "./Header.less";
import { withRouter } from "react-router";
import {
  fetchAllNotification,
  seenNotification,
} from "../../redux/actions/notification";
import config from "../../utils/config";
import { changeLocale } from "containers/LanguageProvider/actions";
import { createStructuredSelector } from "reselect";
import { connect } from "react-redux";
import { compose } from "redux";
import {
  selectAuthGroup,
  selectBuildingCluster,
  selectNotifications,
  selectUserDetail,
} from "redux/selectors";
import { makeSelectLocale } from "containers/LanguageProvider/selectors";
import { getFullLinkImage } from "connection";
const { SubMenu } = Menu;
const { Title, Text } = Typography;

class Header extends PureComponent {
  handleClickMenu = (e) => {
    e.key === "SignOut" && this.props.onSignOut();
    e.key == "profile" &&
      this.props.history.push("/main/account/settings/base");
    e.key == "en" && this.props.dispatch(changeLocale("en"));
    e.key == "vi" && this.props.dispatch(changeLocale("vi"));
  };

  render() {
    const {
      fixed,
      userDetail,
      collapsed,
      notifications,
      dispatch,
      onCollapseChange,
      isMobile,
      auth_group,
      buildingCluster,
      language,
    } = this.props;
    const avatar = userDetail ? getFullLinkImage(userDetail.avatar) : undefined;
    const authGroupName = auth_group ? auth_group.name : "";
    const authGroupNameEn = auth_group ? auth_group.name_en : "";
    const username = userDetail
      ? userDetail.first_name || userDetail.email
      : "";
    const buildingName =
      !!buildingCluster && !!buildingCluster.data
        ? buildingCluster.data.name
        : "";
    const formatMessage = this.props.intl.formatMessage;
    const rightContent = [
      <Menu
        key="user"
        mode="horizontal"
        onClick={this.handleClickMenu}
        selectedKeys={[]}
      >
        <SubMenu
          title={
            <Fragment>
              <Row type="flex" align="middle" style={{ height: 64 }}>
                <Col style={{ lineHeight: 0, textAlign: "right" }}>
                  <div style={{ lineHeight: "18px", fontSize: 18 }}>
                    <span style={{ color: "#999", marginRight: 4 }}>Hi,</span>
                    <span>{username}</span>
                  </div>
                  <Ellipsis
                    lines={1}
                    style={{
                      lineHeight: "14px",
                      fontWeight: "bold",
                      marginTop: 4,
                      fontSize: 10,
                      maxWidth: isMobile ? 100 : 200,
                    }}
                  >
                    <Text mark ellipsis strong>
                      {language === "vi" ? authGroupName : authGroupNameEn}
                    </Text>
                  </Ellipsis>
                </Col>
                <Avatar style={{ marginLeft: 8 }} src={avatar} icon="user" />
              </Row>
            </Fragment>
          }
        >
          <Menu.Item key="profile">
            <Icon type="user" /> {formatMessage(messages.personal)}
          </Menu.Item>
          <Menu.Item key="SignOut">
            <Icon type="logout" /> {formatMessage(messages.logout)}
          </Menu.Item>
        </SubMenu>
      </Menu>,
    ];
    rightContent.unshift(
      <NoticeIcon
        key="bellbellbell"
        ref={(_noticeicon) => (this._noticeicon = _noticeicon)}
        count={notifications.totalUnread}
        onItemClick={async (item) => {
          if (item.is_read == 0) {
            !!dispatch && dispatch(seenNotification(item.id));
          }
          if (
            (item.type == 0 ||
              item.type == 1 ||
              item.type == 2 ||
              /Phản ánh|phản ánh/i.test(item.title_en)) &&
            auth_group.checkRole([config.ALL_ROLE_NAME.REQUEST_LIST])
          ) {
            this._noticeicon && this._noticeicon.popover.click();
            this.props.history.push(
              `/main/ticket/detail/${
                item.request_id || item.service_booking_id
              }?lcid=${item.id}`
            );
          }
          if (item.type == 6) {
            this._noticeicon && this._noticeicon.popover.click();

            window.connection
              .fetchDetailBookingUtility({
                id: item.service_booking_id,
              })
              .then((data) => {
                const record = data.data;
                this.props.history.push(
                  `/main/bookinglist/detail/${item.service_booking_id}/info`,
                  { record }
                );
              });
          }
          if (item.type == 8) {
            this._noticeicon && this._noticeicon.popover.click();
            this.props.history.push("/main/finance/payment-request");
          }
          if (item.type == 10 && /thanh toán/i.test(item.title_en)) {
            this._noticeicon && this._noticeicon.popover.click();
            if (item.service_bill_id) {
              this.props.history.push(
                `/main/finance/bills/detail/${item.service_bill_id}`
              );
            } else if (item.code) {
              try {
                const res = await window.connection.fetchPaymentRequests({
                  code: item.code,
                  pageSize: 1,
                });
                if (res.success && res.data.items.length === 1) {
                  const data = res.data.items[0];
                  this.props.history.push(
                    `/main/finance/payment-request/detail/${data.id}`,
                    {
                      record: data,
                    }
                  );
                } else {
                  this.props.history.push("/main/finance/payment-request");
                }
              } catch (error) {
                this.props.history.push("/main/finance/payment-request");
              }
            } else {
              this.props.history.push("/main/finance/payment-request");
            }
          }
          if (
            item.type == 10 &&
            /Đăng ký thẻ|Đăng ký chuyển/i.test(item.title_en)
          ) {
            this._noticeicon && this._noticeicon.popover.click();
            this.props.history.push(
              `/main/service-utility-form/detail/${item.service_booking_id}`
            );
          }
          if (item.type == 10 && /công việc/i.test(item.title_en)) {
            this._noticeicon && this._noticeicon.popover.click();
            /xóa công việc/i.test(item.title_en)
              ? this.props.history.push("/main/task/list")
              : this.props.history.push(`/main/task/detail/${item.request_id}`);
          }
        }}
        loading={notifications.loading}
        locale={{
          emptyText: formatMessage(messages.noNotifications),
          clear: formatMessage(messages.seeAll),
          viewMore: formatMessage(messages.loadMore),
          notification: formatMessage(messages.notification),
          message: formatMessage(messages.message),
          event: formatMessage(messages.event),
        }}
        onPopupVisibleChange={(visible) => {
          if (visible) {
            !!dispatch && dispatch(fetchAllNotification({ page: 1 }));
          }
        }}
        onViewMore={() => {
          !!dispatch &&
            dispatch(fetchAllNotification({ page: notifications.page + 1 }));
        }}
        onClear={() => {
          !!dispatch && dispatch(seenNotification());
        }}
        clearClose={false}
      >
        <NoticeIcon.Tab
          count={notifications.totalUnread}
          list={notifications.data
            .filter((dd) => dd.title !== "")
            .map((dd) => {
              const {
                type,
                is_read,
                updated_at,
                //  eslint-disable-next-line no-unused-vars
                description, // to remove description from notification
                // eslint-disable-next-line no-unused-vars
                description_en,
                title,
                title_en,
                ...rest
              } = dd;
              return {
                ...rest,
                is_read,
                type,
                title: language === "en" ? title_en : title,
                title_en: title,
                read: is_read,
                datetime: updated_at
                  ? moment.unix(updated_at).fromNow()
                  : undefined,
                extra: undefined,
              };
            })}
          title="notification"
          emptyText={formatMessage(messages.noNotifications)}
          emptyImage="https://gw.alipayobjects.com/zos/rmsportal/wAhyIChODzsoKIOBHcBk.svg"
          showViewMore={notifications.page < notifications.totalPage}
        />
      </NoticeIcon>
    );

    return (
      <Layout.Header
        style={{
          justifyContent: "space-between",
          alignItems: "center",
          padding: 0,
          height: 64,
          display: "flex",
          backgroundColor: "#fff",
          position: "relative",
          boxShadow:
            "0 1px 3px 0 rgba(0, 0, 0, 0.06), 0 1px 6px 0 rgba(0, 0, 0, 0.06)",
        }}
        className={classnames(styles.header, {
          [styles.fixed]: fixed,
          [styles.collapsed]: collapsed,
        })}
        id="layoutHeader"
      >
        <div
          style={{
            width: 64,
            height: 64,
            position: "relative",
            left: 0,
            textAlign: "center",
            fontSize: 18,
            cursor: "pointer",
          }}
          className={styles.button}
          onClick={onCollapseChange.bind(this, !collapsed)}
        >
          <Icon
            type={classnames({
              "menu-unfold": collapsed,
              "menu-fold": !collapsed,
            })}
          />
        </div>
        <div style={{ paddingTop: 16 }}>
          <Title level={2}>{buildingName}</Title>
        </div>
        <div
          style={{
            display: "flex",
            alignItems: "center",
          }}
        >
          {rightContent}
        </div>
      </Layout.Header>
    );
  }
}

Header.propTypes = {
  fixed: PropTypes.bool,
  user: PropTypes.object,
  menus: PropTypes.array,
  collapsed: PropTypes.bool,
  onSignOut: PropTypes.func,
  onCollapseChange: PropTypes.func,
  onAllNotificationsRead: PropTypes.func,
};

const mapStateToProps = createStructuredSelector({
  auth_group: selectAuthGroup(),
  buildingCluster: selectBuildingCluster(),
  notifications: selectNotifications(),
  userDetail: selectUserDetail(),
  language: makeSelectLocale(),
});

function mapDispatchToProps(dispatch) {
  return {
    dispatch,
  };
}

const withConnect = connect(mapStateToProps, mapDispatchToProps);

export default compose(injectIntl, withRouter, withConnect)(Header);
