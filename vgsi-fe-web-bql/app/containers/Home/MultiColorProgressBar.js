import "./process.less";
import React from "react";
export class MultiColorProgressBar extends React.Component {
  constructor(props) {
    super(props);
  }

  checkStylePlatform = () => {
    let { platform } = window.navigator;
    let style = "text-top";
    if (platform.includes("Win")) {
      style = "-webkit-baseline-middle";
    }
    return style;
  };

  render() {
    const parent = this.props;

    let values =
      parent.readings &&
      parent.readings.length &&
      parent.readings.map(function (item, i) {
        if (item.value > 0) {
          return (
            <div
              className="value"
              style={{
                color: item.color,
                width: item.value + "%",
                marginLeft: item.value < 7 && i == 1 ? -10 : 0,
                fontSize:
                  parent.screenwidth <= 900
                    ? 16
                    : parent.screenwidth <= 1440
                    ? 18
                    : 18,
              }}
              key={i}
            >
              <span>{item.value}%</span>
            </div>
          );
        }
      }, this);

    let calibrations =
      parent.readings &&
      parent.readings.length &&
      parent.readings.map(function (item, i) {
        if (item.value > 0) {
          return (
            <div
              className="graduation"
              style={{ color: item.color, width: item.value + "%" }}
              key={i}
            >
              <span>|</span>
            </div>
          );
        }
      }, this);

    let bars =
      parent.readings &&
      parent.readings.length &&
      parent.readings.map(function (item, i) {
        if (item.value > 0) {
          return (
            <div
              className="bar"
              style={{
                backgroundColor: item.color,
                width: item.value + "%",
              }}
              key={i}
            />
          );
        }
      }, this);

    let legends =
      parent.readings &&
      parent.readings.length &&
      parent.readings.map(function (item, i) {
        if (item.value > 0) {
          return (
            <div className="legend" key={i}>
              <span className="dot" style={{ color: item.color }}>
                ●
              </span>
              <span
                className="label"
                style={{ verticalAlign: this.checkStylePlatform() }}
              >
                {item.name}
              </span>
            </div>
          );
        }
      }, this);

    return (
      <div className="multicolor-bar">
        <div className="values">{values == "" ? "" : values}</div>
        <div className="scale">{calibrations == "" ? "" : calibrations}</div>
        <div className="bars">{bars == "" ? "" : bars}</div>
        <div className={window.innerWidth > 1680 ? "legends1" : "legends"}>
          {legends == "" ? "" : legends}
        </div>
      </div>
    );
  }
}
