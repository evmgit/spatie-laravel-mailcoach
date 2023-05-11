<?php

namespace Spatie\Mailcoach\Domain\Shared\Support\Svg;

class BezierCurve
{
    public function __construct(
        protected array $points
    ) {
    }

    public function toPath(): string
    {
        $path = '';

        foreach ($this->points as $index => $point) {
            if ($index === 0) {
                $path = sprintf('M %s,%s', $point[0], $point[1]);
            } else {
                $path .= ' ';
                $path .= $this->command($point, $index);
            }
        }

        return $path;
    }

    protected function command(array $point, int $index): string
    {
        $startControlPoint = $this->controlPoint($this->points[$index - 1] ?? null, $this->points[$index - 2] ?? null, $point);
        $endControlPoint = $this->controlPoint($point, $this->points[$index - 1] ?? null, $this->points[$index + 1] ?? null, true);

        return sprintf(
            'C %s,%s %s,%s %s,%s',
            $startControlPoint[0],
            $startControlPoint[1],
            $endControlPoint[0],
            $endControlPoint[1],
            $point[0],
            $point[1],
        );
    }

    protected function controlPoint(array $current, ?array $previous, ?array $next, bool $reverse = false): array
    {
        $previous = $previous ?: $current;
        $next = $next ?: $current;
        $smoothing = $previous ? 0 : 0.125;

        $opposedLine = $this->line($previous, $next);
        $angle = $opposedLine['angle'] + ($reverse ? pi() : 0);
        $length = $opposedLine['length'] * $smoothing;

        $x = $current[0] + cos($angle) * $length;
        $y = $current[1] + sin($angle) * $length;

        return [$x, $y];
    }

    protected function line(array $pointA, array $pointB): array
    {
        $lengthX = $pointB[0] - $pointA[0];
        $lengthY = $pointB[1] - $pointA[1];

        return [
            'length' => sqrt(pow($lengthX, 2) + pow($lengthY, 2)),
            'angle' => atan2($lengthY, $lengthX),
        ];
    }
}
