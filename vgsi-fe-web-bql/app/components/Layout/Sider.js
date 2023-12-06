import { Layout } from "antd";
import classnames from "classnames";
import PropTypes from "prop-types";
import React, { PureComponent } from "react";
import config from "../../utils/config";
import ScrollBar from "../ScrollBar";
import SiderMenu from "./Menu";
import styles from "./Sider.less";

class Sider extends PureComponent {
  render() {
    const { menus, theme, isMobile, collapsed, onCollapseChange } = this.props;

    return (
      <Layout.Sider
        width={256}
        theme={theme}
        breakpoint="xl"
        trigger={null}
        collapsible
        collapsed={collapsed}
        onBreakpoint={isMobile ? null : onCollapseChange}
        className={styles.sider}
      >
        <div
          style={{
            height: !collapsed ? 156 : null,
            padding: !collapsed ? "0 24px" : null,
            display: "flex",
            alignContent: "center",
            alignItems: "center",
            justifyContent: "center",
            justifyItems: "center",
          }}
          className={classnames({
            [styles.brand]: true,
            [styles.hasBackgroundImage]: !collapsed,
          })}
        >
          <div className={styles.logo}>
            {collapsed ? null : (
              <img
                className={styles.img2}
                style={{ width: 160 }}
                alt="logo_company"
                src={config.logoPath3}
              />
            )}
            {!collapsed ? null : (
              <img
                className={styles.img1}
                style={{ marginBottom: -80 }}
                alt="logo_company"
                src={config.logoPath}
              />
            )}
          </div>
        </div>

        <div style={{ padding: "24px 0" }} className={styles.menuContainer}>
          <ScrollBar
            option={{
              // Disabled horizontal scrolling, https://github.com/utatti/perfect-scrollbar#options
              suppressScrollX: true,
            }}
            className="scroll-luci"
          >
            <SiderMenu
              menus={menus}
              theme={theme}
              isMobile={isMobile}
              collapsed={collapsed}
              onCollapseChange={onCollapseChange}
            />
          </ScrollBar>
        </div>
        {/* <div className={styles.switchTheme}>
          {collapsed ? null : <span>Đổi theme</span>}
          <Switch
            onChange={onThemeChange.bind(
              this,
              theme === 'dark' ? 'light' : 'dark',
            )}
            defaultChecked={theme === 'dark'}
            checkedChildren="Dark"
            unCheckedChildren="Light"
          />
        </div> */}
      </Layout.Sider>
    );
  }
}

Sider.propTypes = {
  menus: PropTypes.array,
  theme: PropTypes.string,
  isMobile: PropTypes.bool,
  collapsed: PropTypes.bool,
  onThemeChange: PropTypes.func,
  onCollapseChange: PropTypes.func,
};

export default Sider;
